window.CryptoTools = require('./cryptotools.js').default
window.Cookies = require('js-cookie')
window.ClipboardJS = require('clipboard')
window.Hammer = require('hammerjs')
window.moment = require('moment')
window.$ = window.jQuery = require('jquery-legacy')

//Load JQuery plugins
require('imports-loader?imports=default|jQuery|$!select2')
require('imports-loader?imports=default|jQuery|$!jstree')

//Can't make it to work
//import jstree from 'jstree'
//jstree = require('jstree');
//window.jstree = require('jstree')(window.$);
//require('jstree')

import css from '../assets/css/legacy.scss'
