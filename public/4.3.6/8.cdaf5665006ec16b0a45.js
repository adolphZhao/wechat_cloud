webpackJsonp([8],{1191:function(e,t,r){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(t,"__esModule",{value:!0});var n=r(3),u=a(n),s=r(117),c=a(s),o=r(1236),i=a(o),l=r(79),d=r(1526),f=r(475),p=a(f),h=r(1449),v=l.config.prefix;t.default=(0,i.default)(h.pageModel,{namespace:"configuration",state:{currentItem:{},modalVisible:!1,modalType:"create",selectedRowKeys:[],isMotion:"true"===window.localStorage.getItem(v+"userIsMotion")},subscriptions:{setup:function(e){var t=e.dispatch;e.history.listen(function(e){"/video-settings"===e.pathname&&t({type:"all",payload:p.default.parse(e.search)})})}},effects:{all:c.default.mark(function e(t,r){var a,n,u=t.payload,s=void 0===u?{}:u,o=r.call,i=r.put;return c.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,o(d.all,s);case 2:if(a=e.sent,n=a.data.list,!a){e.next=7;break}return e.next=7,i({type:"querySuccess",payload:{list:n,pagination:{current:Number(s.page)||1,pageSize:Number(s.pageSize)||20,total:a.total||0}}});case 7:case"end":return e.stop()}},e,this)}),delete:c.default.mark(function e(t,r){var a,n,u,s=t.payload,o=r.call,i=r.put,l=r.select;return c.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,o(d.remove,{id:s});case 2:return a=e.sent,e.next=5,l(function(e){return e.configuration});case 5:if(n=e.sent,u=n.selectedRowKeys,!a.success){e.next=14;break}return e.next=10,i({type:"updateState",payload:{selectedRowKeys:u.filter(function(e){return e!==s})}});case 10:return e.next=12,i({type:"all"});case 12:e.next=15;break;case 14:throw a;case 15:case"end":return e.stop()}},e,this)}),multiDelete:c.default.mark(function e(t,r){var a,n=t.payload,u=r.call,s=r.put;return c.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,u(usersService.remove,n);case 2:if(a=e.sent,!a.success){e.next=10;break}return e.next=6,s({type:"updateState",payload:{selectedRowKeys:[]}});case 6:return e.next=8,s({type:"all"});case 8:e.next=11;break;case 10:throw a;case 11:case"end":return e.stop()}},e,this)}),create:c.default.mark(function e(t,r){var a,n=t.payload,u=r.call,s=r.put;return c.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,u(d.create,n);case 2:if(a=e.sent,!a.success){e.next=10;break}return e.next=6,s({type:"hideModal"});case 6:return e.next=8,s({type:"all"});case 8:e.next=11;break;case 10:throw a;case 11:case"end":return e.stop()}},e,this)}),update:c.default.mark(function e(t,r){var a,n,s,o=t.payload,i=r.select,l=r.call,f=r.put;return c.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,i(function(e){return e.configuration.currentItem.id});case 2:return a=e.sent,n=(0,u.default)({},o,{id:a}),e.next=6,l(d.update,n);case 6:if(s=e.sent,!s.success){e.next=14;break}return e.next=10,f({type:"hideModal"});case 10:return e.next=12,f({type:"all"});case 12:e.next=15;break;case 14:throw s;case 15:case"end":return e.stop()}},e,this)})},reducers:{showModal:function(e,t){var r=t.payload;return(0,u.default)({},e,r,{modalVisible:!0})},hideModal:function(e){return(0,u.default)({},e,{modalVisible:!1})},switchIsMotion:function(e){return window.localStorage.setItem(v+"userIsMotion",!e.isMotion),(0,u.default)({},e,{isMotion:!e.isMotion})}}}),e.exports=t.default},1236:function(e,t,r){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function n(){for(var e={state:{},subscriptions:{},effects:{},reducers:{}},t=[],r={},a=[],n={},u=[],c={},d=[],f={},p=arguments.length,h=Array(p),v=0;v<p;v++)h[v]=arguments[v];var y=h.reduce(function(e,l){return e.namespace=l.namespace,"object"!==(0,o.default)(l.state)||Array.isArray(l.state)?"state"in l&&(e.state=l.state):(i(l.state,t,r),(0,s.default)(e.state,l.state)),i(l.subscriptions,a,n),(0,s.default)(e.subscriptions,l.subscriptions),i(l.effects,u,c),(0,s.default)(e.effects,l.effects),i(l.reducers,d,f),(0,s.default)(e.reducers,l.reducers),e},e);return l(y,"state",r),l(y,"subscriptions",n),l(y,"effects",c),l(y,"reducers",f),y}Object.defineProperty(t,"__esModule",{value:!0});var u=r(292),s=a(u),c=r(65),o=a(c);t.default=n;var i=function(e,t,r){},l=function(e,t,r){}},1449:function(e,t,r){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}var n=r(3),u=a(n),s=r(1236),c=a(s),o={reducers:{updateState:function(e,t){var r=t.payload;return(0,u.default)({},e,r)}}},i=(0,c.default)(o,{state:{list:[],pagination:{showSizeChanger:!0,showQuickJumper:!0,showTotal:function(e){return"Total "+e+" Items"},current:1,total:0}},reducers:{querySuccess:function(e,t){var r=t.payload,a=r.list,n=r.pagination;return(0,u.default)({},e,{list:a,pagination:(0,u.default)({},e.pagination,n)})}}});e.exports={model:o,pageModel:i}},1526:function(e,t,r){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}Object.defineProperty(t,"__esModule",{value:!0}),t.remove=t.create=t.update=t.all=void 0;var n=r(117),u=a(n),s=r(290),c=a(s),o=(t.all=function(){var e=(0,c.default)(u.default.mark(function e(t){return u.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",(0,o.request)({url:l,method:"get",data:t}));case 1:case"end":return e.stop()}},e,this)}));return function(t){return e.apply(this,arguments)}}(),t.update=function(){var e=(0,c.default)(u.default.mark(function e(t){return u.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",(0,o.request)({url:d,method:"put",data:t}));case 1:case"end":return e.stop()}},e,this)}));return function(t){return e.apply(this,arguments)}}(),t.create=function(){var e=(0,c.default)(u.default.mark(function e(t){return u.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",(0,o.request)({url:l,method:"post",data:t}));case 1:case"end":return e.stop()}},e,this)}));return function(t){return e.apply(this,arguments)}}(),t.remove=function(){var e=(0,c.default)(u.default.mark(function e(t){return u.default.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",(0,o.request)({url:d,method:"delete",data:t}));case 1:case"end":return e.stop()}},e,this)}));return function(t){return e.apply(this,arguments)}}(),r(79)),i=o.config.api,l=i.video,d=i.videoOption}});