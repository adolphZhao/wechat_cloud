<?php
namespace App\Supports;

class JavaScriptPacker
{
    const IGNORE = '$1';

    private $_script = '';
    private $_encoding = 62;
    private $_fastDecode = true;
    private $_specialChars = false;

    private $LITERAL_ENCODING = array(
        'None' => 0,
        'Numeric' => 10,
        'Normal' => 62,
        'High ASCII' => 95
    );

    public function __construct($_script, $_encoding = 62, $_fastDecode = true, $_specialChars = false)
    {
        $this->_script = $_script . "\n";
        if (array_key_exists($_encoding, $this->LITERAL_ENCODING))
            $_encoding = $this->LITERAL_ENCODING[$_encoding];
        $this->_encoding = min((int)$_encoding, 95);
        $this->_fastDecode = $_fastDecode;
        $this->_specialChars = $_specialChars;
    }

    public function pack()
    {
        $this->_addParser('_basicCompression');
        if ($this->_specialChars)
            $this->_addParser('_encodeSpecialChars');
        if ($this->_encoding)
            $this->_addParser('_encodeKeywords');

        return $this->_pack($this->_script);
    }

    private function _pack($script)
    {
        for ($i = 0; isset($this->_parsers[$i]); $i++) {
            $script = call_user_func(array(&$this, $this->_parsers[$i]), $script);
        }
        return $script;
    }

    private $_parsers = array();

    private function _addParser($parser)
    {
        $this->_parsers[] = $parser;
    }

    private function _basicCompression($script)
    {
        $parser = new ParseMaster();

        $parser->escapeChar = '\\';

        $parser->add('/\'[^\'\\n\\r]*\'/', self::IGNORE);
        $parser->add('/"[^"\\n\\r]*"/', self::IGNORE);

        $parser->add('/\\/\\/[^\\n\\r]*[\\n\\r]/', ' ');
        $parser->add('/\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\//', ' ');

        $parser->add('/\\s+(\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?)/', '$2');
        $parser->add('/[^\\w\\x24\\/\'"*)\\?:]\\/[^\\/\\n\\r\\*][^\\/\\n\\r]*\\/g?i?/', self::IGNORE);

        if ($this->_specialChars) $parser->add('/;;;[^\\n\\r]+[\\n\\r]/');

        $parser->add('/\\(;;\\)/', self::IGNORE);
        $parser->add('/;+\\s*([};])/', '$2');

        $script = $parser->exec($script);


        $parser->add('/(\\b|\\x24)\\s+(\\b|\\x24)/', '$2 $3');
        $parser->add('/([+\\-])\\s+([+\\-])/', '$2 $3');
        $parser->add('/\\s+/', '');

        return $parser->exec($script);
    }

    private function _encodeSpecialChars($script)
    {
        $parser = new ParseMaster();

        $parser->add('/((\\x24+)([a-zA-Z$_]+))(\\d*)/',
            array('fn' => '_replace_name')
        );

        $regexp = '/\\b_[A-Za-z\\d]\\w*/';

        $keywords = $this->_analyze($script, $regexp, '_encodePrivate');

        $encoded = $keywords['encoded'];

        $parser->add($regexp,
            array(
                'fn' => '_replace_encoded',
                'data' => $encoded
            )
        );
        return $parser->exec($script);
    }

    private function _encodeKeywords($script)
    {

        if ($this->_encoding > 62)
            $script = $this->_escape95($script);

        $parser = new ParseMaster();
        $encode = $this->_getEncoder($this->_encoding);

        $regexp = ($this->_encoding > 62) ? '/\\w\\w+/' : '/\\w+/';

        $keywords = $this->_analyze($script, $regexp, $encode);
        $encoded = $keywords['encoded'];

        $parser->add($regexp,
            array(
                'fn' => '_replace_encoded',
                'data' => $encoded
            )
        );
        if (empty($script)) return $script;
        else {
            return $this->_bootStrap($parser->exec($script), $keywords);
        }
    }

    private function _analyze($script, $regexp, $encode)
    {
        $all = array();
        preg_match_all($regexp, $script, $all);
        $_sorted = array();
        $_encoded = array();
        $_protected = array();
        $all = $all[0];
        if (!empty($all)) {
            $unsorted = array();
            $protected = array();
            $value = array();
            $this->_count = array();
            $i = count($all);
            $j = 0;

            do {
                --$i;
                $word = '$' . $all[$i];
                if (!isset($this->_count[$word])) {
                    $this->_count[$word] = 0;
                    $unsorted[$j] = $word;

                    $values[$j] = call_user_func(array(&$this, $encode), $j);
                    $protected['$' . $values[$j]] = $j++;
                }

                $this->_count[$word]++;
            } while ($i > 0);

            $i = count($unsorted);
            do {
                $word = $unsorted[--$i];
                if (isset($protected[$word]) /*!= null*/) {
                    $_sorted[$protected[$word]] = substr($word, 1);
                    $_protected[$protected[$word]] = true;
                    $this->_count[$word] = 0;
                }
            } while ($i);

            usort($unsorted, array(&$this, '_sortWords'));
            $j = 0;

            do {
                if (!isset($_sorted[$i]))
                    $_sorted[$i] = substr($unsorted[$j++], 1);
                $_encoded[$_sorted[$i]] = $values[$i];
            } while (++$i < count($unsorted));
        }
        return array(
            'sorted' => $_sorted,
            'encoded' => $_encoded,
            'protected' => $_protected);
    }

    private $_count = array();

    private function _sortWords($match1, $match2)
    {
        return $this->_count[$match2] - $this->_count[$match1];
    }

    private function _bootStrap($packed, $keywords)
    {
        $ENCODE = $this->_safeRegExp('$encode\\($count\\)');

        $packed = "'" . $this->_escape($packed) . "'";

        $ascii = min(count($keywords['sorted']), $this->_encoding);
        if ($ascii == 0) $ascii = 1;

        $count = count($keywords['sorted']);

        foreach ($keywords['protected'] as $i => $value) {
            $keywords['sorted'][$i] = '';
        }

        ksort($keywords['sorted']);
        $keywords = "'" . implode('|', $keywords['sorted']) . "'.split('|')";

        $encode = ($this->_encoding > 62) ? '_encode95' : $this->_getEncoder($ascii);
        $encode = $this->_getJSFunction($encode);
        $encode = preg_replace('/_encoding/', '$ascii', $encode);
        $encode = preg_replace('/arguments\\.callee/', '$encode', $encode);
        $inline = '\\$count' . ($ascii > 10 ? '.toString(\\$ascii)' : '');

        if ($this->_fastDecode) {

            $decode = $this->_getJSFunction('_decodeBody');
            if ($this->_encoding > 62)
                $decode = preg_replace('/\\\\w/', '[\\xa1-\\xff]', $decode);

            elseif ($ascii < 36)
                $decode = preg_replace($ENCODE, $inline, $decode);

            if ($count == 0)
                $decode = preg_replace($this->_safeRegExp('($count)\\s*=\\s*1'), '$1=0', $decode, 1);
        }


        $unpack = $this->_getJSFunction('_unpack');
        if ($this->_fastDecode) {

            $this->buffer = $decode;
            $unpack = preg_replace_callback('/\\{/', array(&$this, '_insertFastDecode'), $unpack, 1);
        }
        $unpack = preg_replace('/"/', "'", $unpack);
        if ($this->_encoding > 62) { // high-ascii

            $unpack = preg_replace('/\'\\\\\\\\b\'\s*\\+|\\+\s*\'\\\\\\\\b\'/', '', $unpack);
        }
        if ($ascii > 36 || $this->_encoding > 62 || $this->_fastDecode) {

            $this->buffer = $encode;
            $unpack = preg_replace_callback('/\\{/', array(&$this, '_insertFastEncode'), $unpack, 1);
        } else {

            $unpack = preg_replace($ENCODE, $inline, $unpack);
        }

        $unpackPacker = new JavaScriptPacker($unpack, 0, false, true);
        $unpack = $unpackPacker->pack();

        $params = array($packed, $ascii, $count, $keywords);
        if ($this->_fastDecode) {
            $params[] = 0;
            $params[] = '{}';
        }
        $params = implode(',', $params);

        return 'eval(' . $unpack . '(' . $params . "))\n";
    }

    private $buffer;

    private function _insertFastDecode($match)
    {
        return '{' . $this->buffer . ';';
    }

    private function _insertFastEncode($match)
    {
        return '{$encode=' . $this->buffer . ';';
    }

    private function _getEncoder($ascii)
    {
        return $ascii > 10 ? $ascii > 36 ? $ascii > 62 ?
            '_encode95' : '_encode62' : '_encode36' : '_encode10';
    }

    private function _encode10($charCode)
    {
        return $charCode;
    }

    private function _encode36($charCode)
    {
        return base_convert($charCode, 10, 36);
    }

    private function _encode62($charCode)
    {
        $res = '';
        if ($charCode >= $this->_encoding) {
            $res = $this->_encode62((int)($charCode / $this->_encoding));
        }
        $charCode = $charCode % $this->_encoding;

        if ($charCode > 35)
            return $res . chr($charCode + 29);
        else
            return $res . base_convert($charCode, 10, 36);
    }

    private function _encode95($charCode)
    {
        $res = '';
        if ($charCode >= $this->_encoding)
            $res = $this->_encode95($charCode / $this->_encoding);

        return $res . chr(($charCode % $this->_encoding) + 161);
    }

    private function _safeRegExp($string)
    {
        return '/' . preg_replace('/\$/', '\\\$', $string) . '/';
    }

    private function _encodePrivate($charCode)
    {
        return "_" . $charCode;
    }

    private function _escape($script)
    {
        return preg_replace('/([\\\\\'])/', '\\\$1', $script);
    }

    private function _escape95($script)
    {
        return preg_replace_callback(
            '/[\\xa1-\\xff]/',
            array(&$this, '_escape95Bis'),
            $script
        );
    }

    private function _escape95Bis($match)
    {
        return '\x' . ((string)dechex(ord($match)));
    }

    private function _getJSFunction($aName)
    {
        if (defined('self::JSFUNCTION' . $aName))
            return constant('self::JSFUNCTION' . $aName);
        else
            return '';
    }

    const JSFUNCTION_unpack =

        'function($packed, $ascii, $count, $keywords, $encode, $decode) {
while ($count--) {
if ($keywords[$count]) {
$packed = $packed.replace(new RegExp(\'\\\\b\' + $encode($count) + \'\\\\b\', \'g\'), $keywords[$count]);
}
}
return $packed;
}';
    const JSFUNCTION_decodeBody =

        '    if (!\'\'.replace(/^/, String)) {
// decode all the values we need
while ($count--) {
$decode[$encode($count)] = $keywords[$count] || $encode($count);
}
// global replacement function
$keywords = [function ($encoded) {return $decode[$encoded]}];
// generic match
$encode = function () {return \'\\\\w+\'};
// reset the loop counter -  we are now doing a global replace
$count = 1;
}
';
    const JSFUNCTION_encode10 =
        'function($charCode) {
return $charCode;
}';

    const JSFUNCTION_encode36 =
        'function($charCode) {
return $charCode.toString(36);
}';

    const JSFUNCTION_encode62 =
        'function($charCode) {
return ($charCode < _encoding ? \'\' : arguments.callee(parseInt($charCode / _encoding))) +
(($charCode = $charCode % _encoding) > 35 ? String.fromCharCode($charCode + 29) : $charCode.toString(36));
}';

    const JSFUNCTION_encode95 =
        'function($charCode) {
return ($charCode < _encoding ? \'\' : arguments.callee($charCode / _encoding)) +
String.fromCharCode($charCode % _encoding + 161);
}';

}

