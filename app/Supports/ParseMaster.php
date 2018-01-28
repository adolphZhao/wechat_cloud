<?php
namespace App\Supports;

class ParseMaster
{
    public $ignoreCase = false;
    public $escapeChar = '';

    const EXPRESSION = 0;
    const REPLACEMENT = 1;
    const LENGTH = 2;

    private $GROUPS = '/\\(/';//g
    private $SUB_REPLACE = '/\\$\\d/';
    private $INDEXED = '/^\\$\\d+$/';
    private $TRIM = '/([\'"])\\1\\.(.*)\\.\\1\\1$/';
    private $ESCAPE = '/\\\./';//g
    private $QUOTE = '/\'/';
    private $DELETED = '/\\x01[^\\x01]*\\x01/';//g

    public function add($expression, $replacement = '')
    {
        $length = 1 + preg_match_all($this->GROUPS, $this->_internalEscape((string)$expression), $out);

        if (is_string($replacement)) {
            if (preg_match($this->SUB_REPLACE, $replacement)) {
                if (preg_match($this->INDEXED, $replacement)) {
                    $replacement = (int)(substr($replacement, 1)) - 1;
                } else {
                    $quote = preg_match($this->QUOTE, $this->_internalEscape($replacement))
                        ? '"' : "'";
                    $replacement = array(
                        'fn' => '_backReferences',
                        'data' => array(
                            'replacement' => $replacement,
                            'length' => $length,
                            'quote' => $quote
                        )
                    );
                }
            }
        }

        if (!empty($expression)) $this->_add($expression, $replacement, $length);
        else $this->_add('/^$/', $replacement, $length);
    }

    public function exec($string)
    {
        $this->_escaped = array();

        $regexp = '/';
        foreach ($this->_patterns as $reg) {
            $regexp .= '(' . substr($reg[self::EXPRESSION], 1, -1) . ')|';
        }
        $regexp = substr($regexp, 0, -1) . '/';
        $regexp .= ($this->ignoreCase) ? 'i' : '';

        $string = $this->_escape($string, $this->escapeChar);
        $string = preg_replace_callback(
            $regexp,
            array(
                &$this,
                '_replacement'
            ),
            $string
        );
        $string = $this->_unescape($string, $this->escapeChar);

        return preg_replace($this->DELETED, '', $string);
    }

    public function reset()
    {
        $this->_patterns = array();
    }

    private $_escaped = array();  // escaped characters
    private $_patterns = array(); // patterns stored by index

    private function _add()
    {
        $arguments = func_get_args();
        $this->_patterns[] = $arguments;
    }

    private function _replacement($arguments)
    {
        if (empty($arguments)) return '';

        $i = 1;
        $j = 0;

        while (isset($this->_patterns[$j])) {
            $pattern = $this->_patterns[$j++];

            if (isset($arguments[$i]) && ($arguments[$i] != '')) {
                $replacement = $pattern[self::REPLACEMENT];

                if (is_array($replacement) && isset($replacement['fn'])) {

                    if (isset($replacement['data'])) $this->buffer = $replacement['data'];
                    return call_user_func(array(&$this, $replacement['fn']), $arguments, $i);

                } elseif (is_int($replacement)) {
                    return $arguments[$replacement + $i];

                }
                $delete = ($this->escapeChar == '' ||
                    strpos($arguments[$i], $this->escapeChar) === false)
                    ? '' : "\x01" . $arguments[$i] . "\x01";
                return $delete . $replacement;

            } else {
                $i += $pattern[self::LENGTH];
            }
        }
    }

    private function _backReferences($match, $offset)
    {
        $replacement = $this->buffer['replacement'];
        $quote = $this->buffer['quote'];
        $i = $this->buffer['length'];
        while ($i) {
            $replacement = str_replace('$' . $i--, $match[$offset + $i], $replacement);
        }
        return $replacement;
    }

    private function _replace_name($match, $offset)
    {
        $length = strlen($match[$offset + 2]);
        $start = $length - max($length - strlen($match[$offset + 3]), 0);
        return substr($match[$offset + 1], $start, $length) . $match[$offset + 4];
    }

    private function _replace_encoded($match, $offset)
    {
        return $this->buffer[$match[$offset]];
    }

    private $buffer;

    private function _escape($string, $escapeChar)
    {
        if ($escapeChar) {
            $this->buffer = $escapeChar;
            return preg_replace_callback(
                '/\\' . $escapeChar . '(.)' . '/',
                array(&$this, '_escapeBis'),
                $string
            );

        } else {
            return $string;
        }
    }

    private function _escapeBis($match)
    {
        $this->_escaped[] = $match[1];
        return $this->buffer;
    }

    private function _unescape($string, $escapeChar)
    {
        if ($escapeChar) {
            $regexp = '/' . '\\' . $escapeChar . '/';
            $this->buffer = array('escapeChar' => $escapeChar, 'i' => 0);
            return preg_replace_callback
            (
                $regexp,
                array(&$this, '_unescapeBis'),
                $string
            );

        } else {
            return $string;
        }
    }

    private function _unescapeBis()
    {
        if (isset($this->_escaped[$this->buffer['i']])
            && $this->_escaped[$this->buffer['i']] != ''
        ) {
            $temp = $this->_escaped[$this->buffer['i']];
        } else {
            $temp = '';
        }
        $this->buffer['i']++;
        return $this->buffer['escapeChar'] . $temp;
    }

    private function _internalEscape($string)
    {
        return preg_replace($this->ESCAPE, '', $string);
    }
}