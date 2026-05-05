/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/easing-expired.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/easing-expired.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EasingExpired: function() { return /* binding */ EasingExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _easing__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./easing */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/easing.tsx");





function EasingExpired({
  value = _easing__WEBPACK_IMPORTED_MODULE_4__.DEFAULT_EASING,
  onChange
}) {
  const availableOptions = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.EASING_OPTIONS).map(([key, label]) => ({
    key,
    label,
    disabled: !_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.BASE_EASINGS.includes(key)
  }));
  const handleChange = event => {
    if (_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.BASE_EASINGS.includes(event.target.value)) {
      onChange(event.target.value);
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    onChange: handleChange,
    value: value,
    fullWidth: true,
    displayEmpty: true,
    size: "tiny"
  }, availableOptions.map(option => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: option.key,
      value: option.key,
      disabled: option.disabled
    }, option.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/easing.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/easing.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_EASING: function() { return /* binding */ DEFAULT_EASING; },
/* harmony export */   Easing: function() { return /* binding */ Easing; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




const DEFAULT_EASING = 'easeIn';
function Easing({
  value = DEFAULT_EASING,
  onChange
}) {
  const availableOptions = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.EASING_OPTIONS).map(([key, label]) => ({
    key,
    label
  }));
  const handleChange = event => {
    onChange(event.target.value);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    onChange: handleChange,
    value: value,
    fullWidth: true,
    displayEmpty: true,
    size: "tiny"
  }, availableOptions.map(option => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: option.key,
      value: option.key
    }, option.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro-expired.tsx":
/*!***********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro-expired.tsx ***!
  \***********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EffectProExpired: function() { return /* binding */ EffectProExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




function EffectProExpired({
  value,
  onChange
}) {
  const availableOptions = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.EFFECT_OPTIONS).map(([key, label]) => ({
    key,
    label,
    disabled: !_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.BASE_EFFECTS.includes(key)
  }));
  const handleChange = event => {
    if (_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.BASE_EFFECTS.includes(event.target.value)) {
      onChange(event.target.value);
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: handleChange,
    value: value
  }, availableOptions.map(effect => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: effect.key,
      value: effect.key,
      disabled: effect.disabled
    }, effect.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro.tsx":
/*!***************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro.tsx ***!
  \***************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EffectPro: function() { return /* binding */ EffectPro; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




function EffectPro({
  value,
  onChange
}) {
  const availableOptions = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.EFFECT_OPTIONS).map(([key, label]) => ({
    key,
    label
  }));
  const handleChange = event => {
    onChange(event.target.value);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    value: value,
    onChange: handleChange
  }, availableOptions.map(effect => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: effect.key,
      value: effect.key
    }, effect.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/end-expired.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/end-expired.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EndExpired: function() { return /* binding */ EndExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _offset_expired__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./offset-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-expired.tsx");
/* harmony import */ var _offset_indicator__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./offset-indicator */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



function EndExpired(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_offset_expired__WEBPACK_IMPORTED_MODULE_1__.OffsetExpired, _extends({}, props, {
    defaultValue: _offset_indicator__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_END
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/end.tsx":
/*!********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/end.tsx ***!
  \********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   End: function() { return /* binding */ End; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _offset_indicator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./offset-indicator */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function End(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_offset_indicator__WEBPACK_IMPORTED_MODULE_1__.OffsetIndicator, _extends({}, props, {
    defaultValue: _offset_indicator__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_END
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-expired.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-expired.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   OffsetExpired: function() { return /* binding */ OffsetExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);



const DEFAULT_UNIT = '%';
function OffsetExpired({
  value,
  defaultValue
}) {
  const sizeValue = toSizeValue(value ?? String(defaultValue));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.UnstableSizeField, {
    units: [DEFAULT_UNIT],
    value: sizeValue,
    onChange: () => {},
    disabled: true
  });
}
const toSizeValue = value => {
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil.create({
    size: Number(value),
    unit: DEFAULT_UNIT
  }).value;
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx":
/*!*********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx ***!
  \*********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DEFAULT_END: function() { return /* binding */ DEFAULT_END; },
/* harmony export */   DEFAULT_START: function() { return /* binding */ DEFAULT_START; },
/* harmony export */   OffsetIndicator: function() { return /* binding */ OffsetIndicator; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);



const DEFAULT_UNIT = '%';
const DEFAULT_END = 15;
const DEFAULT_START = 85;
function OffsetIndicator({
  value,
  onChange,
  defaultValue
}) {
  const setValue = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(size => {
    onChange(size === null ? DEFAULT_UNIT : String(size));
  }, [onChange]);
  const handleChange = newValue => {
    const nextSize = newValue.size;
    setValue(typeof nextSize === 'number' && Number.isFinite(nextSize) ? nextSize : null);
  };
  const handleBlur = () => {
    if (isEmptyOffsetValue(value)) {
      setValue(defaultValue);
    }
  };
  const toSizeValue = rawValue => {
    const parsedSize = Number(rawValue);
    const normalizedSize = rawValue === null || rawValue === undefined || rawValue === '' || rawValue === DEFAULT_UNIT || !Number.isFinite(parsedSize) ? null : parsedSize;
    return {
      size: normalizedSize,
      unit: DEFAULT_UNIT
    };
  };
  const sizeValue = toSizeValue(value);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.UnstableSizeField, {
    units: [DEFAULT_UNIT],
    value: sizeValue,
    onChange: handleChange,
    onBlur: handleBlur
  });
}
const isEmptyOffsetValue = value => {
  if (value === null || value === undefined || value === '' || value === DEFAULT_UNIT || value === 'null' || value === 'undefined') {
    return true;
  }
  return !Number.isFinite(Number(value));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to-expired.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to-expired.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RelativeToExpired: function() { return /* binding */ RelativeToExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _relative_to__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./relative-to */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to.tsx");




function RelativeToExpired({
  value
}) {
  const availableOptions = Object.entries(_relative_to__WEBPACK_IMPORTED_MODULE_3__.RELATIVE_TO_OPTIONS).map(([key, label]) => ({
    key,
    label
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: () => {},
    value: value,
    disabled: true
  }, availableOptions.map(({
    key,
    label
  }) => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
      key: key,
      value: key
    }, label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to.tsx":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to.tsx ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RELATIVE_TO_OPTIONS: function() { return /* binding */ RELATIVE_TO_OPTIONS; },
/* harmony export */   RelativeTo: function() { return /* binding */ RelativeTo; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const RELATIVE_TO_OPTIONS = {
  viewport: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Viewport', 'elementor-pro'),
  page: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Page', 'elementor-pro')
};
function RelativeTo({
  value,
  onChange
}) {
  const availableOptions = Object.entries(RELATIVE_TO_OPTIONS).map(([key, label]) => ({
    key,
    label
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: event => onChange(event.target.value),
    value: value || 'viewport'
  }, availableOptions.map(({
    key,
    label
  }) => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.MenuListItem, {
      key: key,
      value: key
    }, label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat-expired.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat-expired.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   RepeatExpired: function() { return /* binding */ RepeatExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _repeat__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./repeat */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat.tsx");




function RepeatExpired({
  value,
  onChange
}) {
  const options = [{
    value: _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_OPTIONS.times,
    label: _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_TOOLTIPS.times,
    disabled: value !== _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_OPTIONS.times,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.Number123Icon, {
      fontSize: size
    }),
    showTooltip: true
  }, {
    value: _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_OPTIONS.loop,
    label: _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_TOOLTIPS.loop,
    disabled: value !== _repeat__WEBPACK_IMPORTED_MODULE_3__.REPEAT_OPTIONS.loop,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.RepeatIcon, {
      fontSize: size
    }),
    showTooltip: true
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: () => onChange(''),
    value: value
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   REPEAT_OPTIONS: function() { return /* binding */ REPEAT_OPTIONS; },
/* harmony export */   REPEAT_TOOLTIPS: function() { return /* binding */ REPEAT_TOOLTIPS; },
/* harmony export */   Repeat: function() { return /* binding */ Repeat; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const REPEAT_TOOLTIPS = {
  loop: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Infinite repeat', 'elementor-pro'),
  times: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Enable number', 'elementor-pro')
};
const REPEAT_OPTIONS = {
  loop: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('loop', 'elementor-pro'),
  times: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('times', 'elementor-pro')
};
function Repeat({
  value,
  onChange
}) {
  const options = [{
    value: REPEAT_OPTIONS.times,
    label: REPEAT_TOOLTIPS.times,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.Number123Icon, {
      fontSize: size
    }),
    showTooltip: true
  }, {
    value: REPEAT_OPTIONS.loop,
    label: REPEAT_TOOLTIPS.loop,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.RepeatIcon, {
      fontSize: size
    }),
    showTooltip: true
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: nextValue => onChange(nextValue || ''),
    value: value || ''
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReplayExpired: function() { return /* binding */ ReplayExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);




function ReplayExpired({
  value,
  onChange
}) {
  const options = [{
    value: false,
    disabled: false,
    label: _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__.REPLAY_OPTIONS.no,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.MinusIcon, {
      fontSize: size
    })
  }, {
    value: true,
    disabled: true,
    label: _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__.REPLAY_OPTIONS.yes,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.CheckIcon, {
      fontSize: size
    })
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: onChange,
    value: value
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx":
/*!***********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx ***!
  \***********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Replay: function() { return /* binding */ Replay; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);




function Replay({
  value,
  onChange
}) {
  const options = [{
    value: false,
    label: _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__.REPLAY_OPTIONS.no,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.MinusIcon, {
      fontSize: size
    })
  }, {
    value: true,
    label: _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_2__.REPLAY_OPTIONS.yes,
    renderContent: ({
      size
    }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.CheckIcon, {
      fontSize: size
    })
  }];
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ToggleButtonGroupUi, {
    items: options,
    exclusive: true,
    onChange: onChange,
    value: value
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/start-expired.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/start-expired.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   StartExpired: function() { return /* binding */ StartExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _offset_expired__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./offset-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-expired.tsx");
/* harmony import */ var _offset_indicator__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./offset-indicator */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }



function StartExpired(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_offset_expired__WEBPACK_IMPORTED_MODULE_1__.OffsetExpired, _extends({}, props, {
    defaultValue: _offset_indicator__WEBPACK_IMPORTED_MODULE_2__.DEFAULT_START
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/start.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/start.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Start: function() { return /* binding */ Start; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _offset_indicator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./offset-indicator */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/offset-indicator.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }


function Start(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_offset_indicator__WEBPACK_IMPORTED_MODULE_1__.OffsetIndicator, _extends({}, props, {
    defaultValue: _offset_indicator__WEBPACK_IMPORTED_MODULE_1__.DEFAULT_START
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/times-expired.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/times-expired.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TimesExpired: function() { return /* binding */ TimesExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);


function TimesExpired({
  value
}) {
  const handleChange = () => {};
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberInput, {
    size: "tiny",
    type: "number",
    fullWidth: true,
    disabled: true,
    value: Number.isFinite(Number(value)) ? Number(value) : '',
    onInput: handleChange,
    InputProps: {
      inputProps: {
        min: 1
      }
    }
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/times.tsx":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/times.tsx ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Times: function() { return /* binding */ Times; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);


const DEFAULT_TIMES = 1;
function Times({
  value,
  onChange
}) {
  const handleChange = event => {
    const parsedValue = Number(event.target.value);
    onChange(Number.isFinite(parsedValue) ? Math.max(DEFAULT_TIMES, Math.trunc(parsedValue)) : DEFAULT_TIMES);
  };
  const handleBlur = () => {
    if (!Number.isFinite(Number(value)) || Number(value) < DEFAULT_TIMES) {
      onChange(DEFAULT_TIMES);
    }
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberInput, {
    size: "tiny",
    type: "number",
    fullWidth: true,
    value: Number.isFinite(Number(value)) ? Number(value) : '',
    onInput: handleChange,
    onBlur: handleBlur,
    InputProps: {
      inputProps: {
        min: DEFAULT_TIMES,
        step: 1
      }
    }
  });
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TriggerExpired: function() { return /* binding */ TriggerExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




function TriggerExpired({
  value,
  onChange
}) {
  const availableTriggers = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.TRIGGER_OPTIONS).map(([key, label]) => ({
    key,
    label,
    disabled: !_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.BASE_TRIGGERS.includes(key)
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: event => onChange(event.target.value),
    value: value
  }, availableTriggers.map(trigger => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: trigger.key,
      value: trigger.key,
      disabled: trigger.disabled
    }, trigger.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx":
/*!************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Trigger: function() { return /* binding */ Trigger; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




function Trigger({
  value,
  onChange
}) {
  const availableTriggers = Object.entries(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_1__.TRIGGER_OPTIONS).map(([key, label]) => ({
    key,
    label
  }));
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Select, {
    fullWidth: true,
    displayEmpty: true,
    size: "tiny",
    onChange: event => onChange(event.target.value),
    value: value
  }, availableTriggers.map(trigger => {
    return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.MenuListItem, {
      key: trigger.key,
      value: trigger.key
    }, trigger.label);
  }));
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/content.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/content.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Content: function() { return /* binding */ Content; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../context/collapsable-section-context */ "./packages/packages/pro/editor-interactions-extended/src/context/collapsable-section-context.tsx");
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _controls_move__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./controls/move */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/move.tsx");
/* harmony import */ var _controls_opacity__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./controls/opacity */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/opacity.tsx");
/* harmony import */ var _controls_rotate__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./controls/rotate */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/rotate.tsx");
/* harmony import */ var _controls_scale__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./controls/scale */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/scale.tsx");
/* harmony import */ var _controls_skew__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./controls/skew */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/skew.tsx");
/* harmony import */ var _ui_collapsable_section__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./ui/collapsable-section */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/collapsable-section.tsx");











const Content = ({
  stopPosition
}) => {
  const scrollRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const {
    selectStop
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_3__.useCustomEffect)();
  const {
    scrollPosition,
    setScrollPosition
  } = (0,_context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_2__.useScrollPosition)();
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    selectStop(stopPosition);
  }, [selectStop, stopPosition]);
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const element = scrollRef.current;
    if (element) {
      element.scrollTop = scrollPosition;
    }
  }, [scrollPosition]);
  const handleScroll = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    const element = scrollRef.current;
    if (element) {
      setScrollPosition(element.scrollTop);
    }
  }, [setScrollPosition]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    p: 1.5,
    ref: scrollRef,
    onScroll: handleScroll,
    sx: {
      maxHeight: 245,
      overflowY: 'auto'
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.List, {
    disablePadding: true,
    component: "div"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    pb: 1.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_opacity__WEBPACK_IMPORTED_MODULE_5__.Opacity, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_collapsable_section__WEBPACK_IMPORTED_MODULE_9__.CollapsableSection, {
    label: "Scale"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_scale__WEBPACK_IMPORTED_MODULE_7__.Scale, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_collapsable_section__WEBPACK_IMPORTED_MODULE_9__.CollapsableSection, {
    label: "Move"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_move__WEBPACK_IMPORTED_MODULE_4__.Move, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_collapsable_section__WEBPACK_IMPORTED_MODULE_9__.CollapsableSection, {
    label: "Rotate"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_rotate__WEBPACK_IMPORTED_MODULE_6__.Rotate, null)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_collapsable_section__WEBPACK_IMPORTED_MODULE_9__.CollapsableSection, {
    label: "Skew"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_controls_skew__WEBPACK_IMPORTED_MODULE_8__.Skew, null))));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/move.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/move.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Move: function() { return /* binding */ Move; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _ui_axis__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ui/axis */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/axis.tsx");






const UNITS = ['px', '%', 'em', 'rem', 'vw'];
const DEFULAT_UNIT = 'px';
const configs = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Move X', 'elementor-pro'),
  bind: 'x',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon, {
    fontSize: 'tiny'
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Move Y', 'elementor-pro'),
  bind: 'y',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownSmallIcon, {
    fontSize: 'tiny'
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Move Z', 'elementor-pro'),
  bind: 'z',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowDownLeftIcon, {
    fontSize: 'tiny'
  })
}];
const Move = () => {
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__.useCustomEffect)('move');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_axis__WEBPACK_IMPORTED_MODULE_5__.Axis, {
    configs: configs,
    value: value?.value,
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.moveTransformPropTypeUtil,
    setValue: setValue,
    units: UNITS,
    defaultUnit: DEFULAT_UNIT
  });
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/opacity.tsx":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/opacity.tsx ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Opacity: function() { return /* binding */ Opacity; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _ui_field_layout__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ui/field-layout */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/field-layout.tsx");






const UNITS = ['%'];
const DEFAULT_UNIT = '%';
const Opacity = () => {
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__.useCustomEffect)('opacity');
  const handleChange = size => {
    setValue(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.sizePropTypeUtil.create(size));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_field_layout__WEBPACK_IMPORTED_MODULE_5__.FieldLayout, {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Opacity', 'elementor-pro')
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.UnstableSizeField, {
    units: UNITS,
    defaultUnit: DEFAULT_UNIT,
    value: value?.value,
    onChange: handleChange
  }));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/rotate.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/rotate.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Rotate: function() { return /* binding */ Rotate; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _ui_axis__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ui/axis */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/axis.tsx");






const UNITS = ['deg', 'rad', 'grad', 'turn'];
const DEFULAT_UNIT = 'deg';
const configs = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Rotate X', 'elementor-pro'),
  bind: 'x',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.Arrow360Icon, {
    fontSize: 'tiny'
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Rotate Y', 'elementor-pro'),
  bind: 'y',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.Arrow360Icon, {
    fontSize: "tiny",
    style: {
      transform: 'scaleX(-1) rotate(-90deg)'
    }
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Rotate Z', 'elementor-pro'),
  bind: 'z',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.RotateClockwiseIcon, {
    fontSize: 'tiny'
  })
}];
const Rotate = () => {
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__.useCustomEffect)('rotate');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_axis__WEBPACK_IMPORTED_MODULE_5__.Axis, {
    configs: configs,
    value: value?.value,
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.rotateTransformPropTypeUtil,
    setValue: setValue,
    units: UNITS,
    defaultUnit: DEFULAT_UNIT
  });
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/scale.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/scale.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SCALE_STEP: function() { return /* binding */ SCALE_STEP; },
/* harmony export */   Scale: function() { return /* binding */ Scale; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _ui_field_layout__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../ui/field-layout */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/field-layout.tsx");
/* harmony import */ var _ui_number_field__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../ui/number-field */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/number-field.tsx");








const SCALE_STEP = 0.1;
const configs = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Scale X', 'elementor-pro'),
  bind: 'x',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ArrowRightIcon, {
    fontSize: 'tiny'
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Scale Y', 'elementor-pro'),
  bind: 'y',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_3__.ArrowDownSmallIcon, {
    fontSize: 'tiny'
  })
}];
const Scale = () => {
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_5__.useCustomEffect)('scale');
  const propValue = value?.value;
  const handleChange = (newValue, bind) => {
    setValue(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.scaleTransformPropTypeUtil.create({
      ...propValue,
      ...{
        [bind]: newValue
      }
    }));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.SectionContent, null, configs.map(({
    bind,
    label,
    startIcon
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_field_layout__WEBPACK_IMPORTED_MODULE_6__.FieldLayout, {
    key: label,
    label: label
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_number_field__WEBPACK_IMPORTED_MODULE_7__.NumberField, {
    step: SCALE_STEP,
    value: propValue?.[bind],
    onChange: newValue => handleChange(newValue, bind),
    startIcon: startIcon
  }))));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/skew.tsx":
/*!************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/controls/skew.tsx ***!
  \************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Skew: function() { return /* binding */ Skew; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _ui_axis__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../ui/axis */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/axis.tsx");






const UNITS = ['deg', 'rad', 'grad', 'turn'];
const DEFULAT_UNIT = 'deg';
const configs = [{
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Skew X', 'elementor-pro'),
  bind: 'x',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowRightIcon, {
    fontSize: 'tiny'
  })
}, {
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Skew Y', 'elementor-pro'),
  bind: 'y',
  startIcon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.ArrowLeftIcon, {
    fontSize: "tiny",
    style: {
      transform: 'scaleX(-1) rotate(-90deg)'
    }
  })
}];
const Skew = () => {
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_4__.useCustomEffect)('skew');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_axis__WEBPACK_IMPORTED_MODULE_5__.Axis, {
    configs: configs,
    value: value?.value,
    propTypeUtil: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_1__.skewTransformPropTypeUtil,
    setValue: setValue,
    units: UNITS,
    defaultUnit: DEFULAT_UNIT
  });
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects-expired.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects-expired.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomEffectExpired: function() { return /* binding */ CustomEffectExpired; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _ui_edit_toggle_button__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ui/edit-toggle-button */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/edit-toggle-button.tsx");
/* harmony import */ var _ui_row__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ui/row */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/row.tsx");



const CustomEffectExpired = () => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_row__WEBPACK_IMPORTED_MODULE_2__.Row, {
    justify: "flex-end"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_edit_toggle_button__WEBPACK_IMPORTED_MODULE_1__.EditToggleButton, {
    disabled: true
  }));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects.tsx":
/*!*************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects.tsx ***!
  \*************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomEffect: function() { return /* binding */ CustomEffect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _popover__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./popover */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/popover.tsx");



const CustomEffect = ({
  value,
  onChange
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_1__.CustomEffectProvider, {
    value: value,
    onChange: onChange
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_popover__WEBPACK_IMPORTED_MODULE_2__.Popover, {
    autoOpen: !value
  }));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/keyframes.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/keyframes.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Keyframes: function() { return /* binding */ Keyframes; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../context/collapsable-section-context */ "./packages/packages/pro/editor-interactions-extended/src/context/collapsable-section-context.tsx");
/* harmony import */ var _content__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./content */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/content.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }





const Keyframes = () => {
  const {
    getTabsProps,
    getTabProps,
    getTabPanelProps
  } = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.useTabs)('from');
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_3__.CollapsableSectionProvider, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tabs, _extends({
    size: "small",
    variant: "fullWidth",
    "aria-label": (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Custom Effect', 'elementor-pro')
  }, getTabsProps()), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('From', 'elementor-pro')
  }, getTabProps('from'))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Tab, _extends({
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('To', 'elementor-pro')
  }, getTabProps('to')))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TabPanel, _extends({
    sx: {
      p: 0
    }
  }, getTabPanelProps('from')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_content__WEBPACK_IMPORTED_MODULE_4__.Content, {
    key: "from",
    stopPosition: 0
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.TabPanel, _extends({
    sx: {
      p: 0
    }
  }, getTabPanelProps('to')), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_content__WEBPACK_IMPORTED_MODULE_4__.Content, {
    key: "to",
    stopPosition: 100
  })));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/popover.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/popover.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Popover: function() { return /* binding */ Popover; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../../context/custom-effect/custom-effect-context */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx");
/* harmony import */ var _keyframes__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./keyframes */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/keyframes.tsx");
/* harmony import */ var _ui_edit_toggle_button__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./ui/edit-toggle-button */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/edit-toggle-button.tsx");
/* harmony import */ var _ui_row__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./ui/row */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/row.tsx");
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }










const Popover = ({
  autoOpen: shouldOpen
}) => {
  const [autoOpen, setAutoOpen] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(!!shouldOpen);
  const popupState = (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.usePopupState)({
    variant: 'popover'
  });
  const anchorRef = (0,react__WEBPACK_IMPORTED_MODULE_0__.useRef)(null);
  const {
    value,
    setValue
  } = (0,_context_custom_effect_custom_effect_context__WEBPACK_IMPORTED_MODULE_5__.useCustomEffect)('opacity');
  const openPopover = () => {
    popupState.open(anchorRef.current);
  };
  const closePopover = () => {
    popupState.close();
    setAutoOpen(false);
    if (!value) {
      setValue(null);
    }
  };
  (0,react__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (autoOpen && anchorRef.current) {
      popupState.open(anchorRef.current);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [autoOpen]);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_row__WEBPACK_IMPORTED_MODULE_8__.Row, {
    justify: "flex-end"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_ui_edit_toggle_button__WEBPACK_IMPORTED_MODULE_7__.EditToggleButton, {
    ref: anchorRef,
    onClick: openPopover,
    disabled: popupState.isOpen
  }), popupState.isOpen && /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Popover, _extends({
    anchorOrigin: {
      vertical: 'center',
      horizontal: 'right'
    },
    transformOrigin: {
      vertical: 50,
      horizontal: -25
    }
  }, (0,_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.bindPopover)(popupState), {
    onClose: closePopover
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.PopoverHeader, {
    icon: /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_2__.SwipeIcon, {
      fontSize: "tiny"
    }),
    title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Custom effect', 'elementor-pro'),
    onClose: closePopover
  }), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_1__.PopoverBody, {
    width: 297,
    height: 280
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_keyframes__WEBPACK_IMPORTED_MODULE_6__.Keyframes, null))));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/axis.tsx":
/*!******************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/axis.tsx ***!
  \******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Axis: function() { return /* binding */ Axis; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _field_layout__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./field-layout */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/field-layout.tsx");





const Axis = ({
  configs,
  units,
  value,
  defaultUnit,
  setValue,
  propTypeUtil
}) => {
  const handleChange = (size, bind) => {
    const propValue = propTypeUtil.create({
      ...value,
      ...{
        [bind]: _elementor_editor_props__WEBPACK_IMPORTED_MODULE_3__.sizePropTypeUtil.create(size)
      }
    });
    setValue(propValue);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_2__.SectionContent, null, configs.map(({
    label,
    bind,
    startIcon
  }) => /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_field_layout__WEBPACK_IMPORTED_MODULE_4__.FieldLayout, {
    key: label,
    label: label
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.UnstableSizeField, {
    startIcon: startIcon,
    units: units,
    value: value?.[bind]?.value,
    defaultUnit: defaultUnit,
    onChange: size => handleChange(size, bind)
  }))));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/collapsable-section.tsx":
/*!*********************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/collapsable-section.tsx ***!
  \*********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CollapsableSection: function() { return /* binding */ CollapsableSection; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-editing-panel */ "@elementor/editor-editing-panel");
/* harmony import */ var _elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-ui */ "@elementor/editor-ui");
/* harmony import */ var _elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../context/collapsable-section-context */ "./packages/packages/pro/editor-interactions-extended/src/context/collapsable-section-context.tsx");






const CollapsableSection = ({
  label,
  children
}) => {
  const {
    isOpen,
    toggle
  } = (0,_context_collapsable_section_context__WEBPACK_IMPORTED_MODULE_4__.useSectionOpen)(label);
  const id = (0,react__WEBPACK_IMPORTED_MODULE_0__.useId)();
  const labelId = `label-${id}`;
  const contentId = `content-${id}`;
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemButton, {
    id: labelId,
    "aria-controls": contentId,
    "aria-label": `${label} section`,
    onClick: toggle,
    sx: {
      '&:hover': {
        backgroundColor: 'transparent'
      },
      paddingInline: 0
    }
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    direction: "row",
    alignItems: "center",
    justifyItems: "start",
    flexGrow: 1,
    gap: 0.5
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.ListItemText, {
    secondary: label,
    secondaryTypographyProps: {
      color: 'text.primary',
      variant: 'caption',
      fontWeight: 'bold'
    },
    sx: {
      flexGrow: 0,
      flexShrink: 1,
      marginInlineEnd: 1
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_ui__WEBPACK_IMPORTED_MODULE_2__.CollapseIcon, {
    open: isOpen,
    color: "secondary",
    fontSize: "tiny",
    sx: {
      ml: 1
    }
  })), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Collapse, {
    id: contentId,
    "aria-labelledby": labelId,
    in: isOpen,
    timeout: "auto"
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Stack, {
    gap: 2.5,
    pb: 2
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_editing_panel__WEBPACK_IMPORTED_MODULE_1__.SectionContent, null, children))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.Divider, null));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/edit-toggle-button.tsx":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/edit-toggle-button.tsx ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EditToggleButton: function() { return /* binding */ EditToggleButton; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/icons */ "@elementor/icons");
/* harmony import */ var _elementor_icons__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }




const EditToggleButton = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.forwardRef)(({
  disabled = false,
  onClick
}, ref) => {
  const openProps = {};
  const isEditing = !Boolean(disabled);
  if (isEditing) {
    Object.assign(openProps, {
      'aria-haspopup': 'dialog',
      'aria-expanded': isEditing,
      onClick
    });
  }
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.ToggleButton, _extends({
    ref: ref,
    value: "custom",
    size: "tiny",
    disabled: disabled
  }, openProps), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_icons__WEBPACK_IMPORTED_MODULE_1__.PencilIcon, {
    fontSize: "tiny"
  }));
});

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/field-layout.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/field-layout.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FieldLayout: function() { return /* binding */ FieldLayout; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__);



const FieldLayout = ({
  label,
  children
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.PopoverGridContainer, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6
  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.ControlFormLabel, null, label)), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_2__.Grid, {
    item: true,
    xs: 6
  }, children));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/number-field.tsx":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/number-field.tsx ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   NumberField: function() { return /* binding */ NumberField; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/editor-controls */ "@elementor/editor-controls");
/* harmony import */ var _elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__);




const NumberField = ({
  value,
  onChange,
  startIcon,
  step,
  min,
  max
}) => {
  const numberValue = value?.value;
  const handleChange = event => {
    const newValue = '' !== event.target.value ? Number(event.target.value) : null;
    onChange(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.create(newValue));
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_editor_controls__WEBPACK_IMPORTED_MODULE_1__.NumberInput, {
    size: "tiny",
    type: "number",
    fullWidth: true,
    value: isValid(numberValue) ? numberValue : '',
    onInput: handleChange,
    inputProps: {
      step,
      min,
      max
    },
    InputProps: {
      startAdornment: startIcon ? /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_3__.InputAdornment, {
        position: "start"
      }, startIcon) : undefined
    }
  });
};
const isValid = value => {
  return value !== null && value !== undefined && !Number.isNaN(Number(value));
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/row.tsx":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/ui/row.tsx ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Row: function() { return /* binding */ Row; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/ui */ "@elementor/ui");
/* harmony import */ var _elementor_ui__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__);


const Row = ({
  justify,
  children
}) => {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_elementor_ui__WEBPACK_IMPORTED_MODULE_1__.Stack, {
    direction: "row",
    justifyContent: justify
  }, children);
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/context/collapsable-section-context.tsx":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/context/collapsable-section-context.tsx ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CollapsableSectionProvider: function() { return /* binding */ CollapsableSectionProvider; },
/* harmony export */   useScrollPosition: function() { return /* binding */ useScrollPosition; },
/* harmony export */   useSectionOpen: function() { return /* binding */ useSectionOpen; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


const CollapsableSectionContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const CollapsableSectionProvider = ({
  children
}) => {
  const [openSections, setOpenSections] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const [scrollPosition, setScrollPosition] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  const setSectionOpen = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)((label, open) => {
    setOpenSections(prev => ({
      ...prev,
      [label]: open
    }));
  }, []);
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CollapsableSectionContext.Provider, {
    value: {
      openSections,
      scrollPosition,
      setScrollPosition,
      setSectionOpen
    }
  }, children);
};
const useScrollPosition = () => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(CollapsableSectionContext);
  if (!context) {
    throw new Error('useScrollPosition must be used within SectionOpenProvider');
  }
  const {
    scrollPosition,
    setScrollPosition
  } = context;
  return {
    scrollPosition,
    setScrollPosition
  };
};
const useSectionOpen = label => {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(CollapsableSectionContext);
  if (!context) {
    throw new Error('useSectionOpen must be used within SectionOpenProvider');
  }
  const {
    openSections,
    setSectionOpen
  } = context;
  const isOpen = openSections[label] ?? false;
  const toggle = (0,react__WEBPACK_IMPORTED_MODULE_0__.useCallback)(() => {
    setSectionOpen(label, !isOpen);
  }, [setSectionOpen, isOpen, label]);
  return {
    isOpen,
    toggle
  };
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/custom-effect-context.tsx ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CustomEffectProvider: function() { return /* binding */ CustomEffectProvider; },
/* harmony export */   useCustomEffect: function() { return /* binding */ useCustomEffect; }
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_keyframe_factory__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/keyframe-factory */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-factory.ts");
/* harmony import */ var _utils_keyframe_selectors__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/keyframe-selectors */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-selectors.ts");
/* harmony import */ var _utils_merge_keyframes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/merge-keyframes */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/merge-keyframes.ts");





const CustomEffectContext = /*#__PURE__*/(0,react__WEBPACK_IMPORTED_MODULE_0__.createContext)(null);
const CustomEffectProvider = ({
  children,
  value,
  onChange
}) => {
  const [currentStopPosition, setStopPosition] = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  const updateKeyframeSetting = (key, newValue) => {
    const updatedKeyframes = (0,_utils_merge_keyframes__WEBPACK_IMPORTED_MODULE_3__.mergeKeyframeSettings)((0,_utils_keyframe_selectors__WEBPACK_IMPORTED_MODULE_2__.getKeyframeStops)(value), currentStopPosition, {
      [key]: newValue
    });
    const customEffect = _utils_keyframe_factory__WEBPACK_IMPORTED_MODULE_1__.keyframeFactory.buildCustomEffect(updatedKeyframes);
    onChange(customEffect);
  };
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(CustomEffectContext.Provider, {
    value: {
      value,
      setStopPosition,
      currentStopPosition,
      setValue: updateKeyframeSetting
    }
  }, children);
};
function useCustomEffect(key) {
  const context = (0,react__WEBPACK_IMPORTED_MODULE_0__.useContext)(CustomEffectContext);
  if (!context) {
    throw new Error('useCustomEffect must be used within CustomEffectProvider');
  }
  const {
    value,
    setValue,
    setStopPosition,
    currentStopPosition
  } = context;
  if (!key) {
    return {
      selectStop: setStopPosition
    };
  }
  return {
    value: (0,_utils_keyframe_selectors__WEBPACK_IMPORTED_MODULE_2__.getKeyframeSetting)(value, currentStopPosition, key),
    setValue: newValue => setValue(key, newValue),
    selectStop: setStopPosition
  };
}

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-factory.ts":
/*!****************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-factory.ts ***!
  \****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   keyframeFactory: function() { return /* binding */ keyframeFactory; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-props */ "@elementor/editor-props");
/* harmony import */ var _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__);

const createPropValue = ($$type, value) => {
  return {
    $$type,
    value
  };
};
const createStopPosition = stopPosition => {
  return _elementor_editor_props__WEBPACK_IMPORTED_MODULE_0__.sizePropTypeUtil.create({
    size: stopPosition,
    unit: '%'
  });
};
const createKeyframeSettings = (settings = {}) => {
  return createPropValue('keyframe-stop-settings', settings);
};
const createKeyframeStop = (stopPosition, settings = createKeyframeSettings()) => {
  return createPropValue('keyframe-stop', {
    stop: createStopPosition(stopPosition),
    settings
  });
};
const createKeyframes = keyframeStop => {
  return createPropValue('keyframes', keyframeStop);
};
const buildCustomEffect = keyframeStop => {
  return createPropValue('custom-effect', {
    keyframes: createKeyframes(keyframeStop)
  });
};
const keyframeFactory = {
  createKeyframeStop,
  createKeyframeSettings,
  buildCustomEffect
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-selectors.ts":
/*!******************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-selectors.ts ***!
  \******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   findKeyframeAtStopPosition: function() { return /* binding */ findKeyframeAtStopPosition; },
/* harmony export */   getKeyframeSetting: function() { return /* binding */ getKeyframeSetting; },
/* harmony export */   getKeyframeStops: function() { return /* binding */ getKeyframeStops; },
/* harmony export */   getSettingFromKeyframe: function() { return /* binding */ getSettingFromKeyframe; },
/* harmony export */   getStopPosition: function() { return /* binding */ getStopPosition; }
/* harmony export */ });
const getKeyframeStops = customEffect => {
  return customEffect?.value?.keyframes?.value ?? [];
};
const getStopPosition = stop => {
  return stop.value.stop.value.size;
};
const findKeyframeAtStopPosition = (customEffect, stopPosition) => {
  return getKeyframeStops(customEffect).find(stop => getStopPosition(stop) === stopPosition);
};
const getSettingFromKeyframe = (stop, key) => {
  return stop.value.settings.value[key] ?? null;
};
const getKeyframeSetting = (customEffect, stopPosition, key) => {
  const keyframeStop = findKeyframeAtStopPosition(customEffect, stopPosition);
  if (!keyframeStop) {
    return null;
  }
  return getSettingFromKeyframe(keyframeStop, key);
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/merge-keyframes.ts":
/*!***************************************************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/merge-keyframes.ts ***!
  \***************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mergeKeyframeSettings: function() { return /* binding */ mergeKeyframeSettings; }
/* harmony export */ });
/* harmony import */ var _keyframe_factory__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./keyframe-factory */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-factory.ts");
/* harmony import */ var _keyframe_selectors__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./keyframe-selectors */ "./packages/packages/pro/editor-interactions-extended/src/context/custom-effect/utils/keyframe-selectors.ts");


const mergeKeyframeSettings = (existing, position, newSettings) => {
  const stops = structuredClone(existing);
  const stopIndex = stops.findIndex(stop => (0,_keyframe_selectors__WEBPACK_IMPORTED_MODULE_1__.getStopPosition)(stop) === position);
  let updatedStop;
  if (stopIndex !== -1) {
    updatedStop = mergeIntoKeyframe(stops[stopIndex], newSettings);
  } else {
    const settings = _keyframe_factory__WEBPACK_IMPORTED_MODULE_0__.keyframeFactory.createKeyframeSettings(newSettings);
    updatedStop = _keyframe_factory__WEBPACK_IMPORTED_MODULE_0__.keyframeFactory.createKeyframeStop(position, settings);
  }
  return upsertKeyframe(stops, updatedStop, stopIndex);
};
const mergeIntoKeyframe = (existing, newSettings) => {
  const currentSettings = existing.value.settings.value;
  const mergedSettings = _keyframe_factory__WEBPACK_IMPORTED_MODULE_0__.keyframeFactory.createKeyframeSettings({
    ...currentSettings,
    ...newSettings
  });
  return _keyframe_factory__WEBPACK_IMPORTED_MODULE_0__.keyframeFactory.createKeyframeStop((0,_keyframe_selectors__WEBPACK_IMPORTED_MODULE_1__.getStopPosition)(existing), mergedSettings);
};
const upsertKeyframe = (stops, stop, stopIndex) => {
  if (stopIndex !== -1) {
    stops[stopIndex] = stop;
    return stops;
  }
  const insertAt = stops.findIndex(s => (0,_keyframe_selectors__WEBPACK_IMPORTED_MODULE_1__.getStopPosition)(s) > (0,_keyframe_selectors__WEBPACK_IMPORTED_MODULE_1__.getStopPosition)(stop));
  if (insertAt === -1) {
    stops.push(stop);
  } else {
    stops.splice(insertAt, 0, stop);
  }
  return stops;
};

/***/ }),

/***/ "./packages/packages/pro/editor-interactions-extended/src/init.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/init.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* binding */ init; }
/* harmony export */ });
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/editor-interactions */ "@elementor/editor-interactions");
/* harmony import */ var _elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _elementor_license_api__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @elementor/license-api */ "@elementor/license-api");
/* harmony import */ var _elementor_license_api__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_elementor_license_api__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_controls_easing__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./components/controls/easing */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/easing.tsx");
/* harmony import */ var _components_controls_easing_expired__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./components/controls/easing-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/easing-expired.tsx");
/* harmony import */ var _components_controls_effect_pro__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./components/controls/effect-pro */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro.tsx");
/* harmony import */ var _components_controls_effect_pro_expired__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./components/controls/effect-pro-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/effect-pro-expired.tsx");
/* harmony import */ var _components_controls_end__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./components/controls/end */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/end.tsx");
/* harmony import */ var _components_controls_end_expired__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./components/controls/end-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/end-expired.tsx");
/* harmony import */ var _components_controls_relative_to__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./components/controls/relative-to */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to.tsx");
/* harmony import */ var _components_controls_relative_to_expired__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./components/controls/relative-to-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/relative-to-expired.tsx");
/* harmony import */ var _components_controls_repeat__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./components/controls/repeat */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat.tsx");
/* harmony import */ var _components_controls_repeat_expired__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./components/controls/repeat-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/repeat-expired.tsx");
/* harmony import */ var _components_controls_replay__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./components/controls/replay */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay.tsx");
/* harmony import */ var _components_controls_replay_expired__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./components/controls/replay-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/replay-expired.tsx");
/* harmony import */ var _components_controls_start__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./components/controls/start */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/start.tsx");
/* harmony import */ var _components_controls_start_expired__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./components/controls/start-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/start-expired.tsx");
/* harmony import */ var _components_controls_times__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./components/controls/times */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/times.tsx");
/* harmony import */ var _components_controls_times_expired__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./components/controls/times-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/times-expired.tsx");
/* harmony import */ var _components_controls_trigger__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./components/controls/trigger */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger.tsx");
/* harmony import */ var _components_controls_trigger_expired__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./components/controls/trigger-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/controls/trigger-expired.tsx");
/* harmony import */ var _components_custom_effects_custom_effects__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./components/custom-effects/custom-effects */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects.tsx");
/* harmony import */ var _components_custom_effects_custom_effects_expired__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./components/custom-effects/custom-effects-expired */ "./packages/packages/pro/editor-interactions-extended/src/components/custom-effects/custom-effects-expired.tsx");






















async function init() {
  const isLicenseExpired = await (0,_elementor_license_api__WEBPACK_IMPORTED_MODULE_1__.fetchLicenseStatus)().catch(() => true);
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'replay',
    component: isLicenseExpired ? _components_controls_replay_expired__WEBPACK_IMPORTED_MODULE_13__.ReplayExpired : _components_controls_replay__WEBPACK_IMPORTED_MODULE_12__.Replay,
    options: Object.keys(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.REPLAY_OPTIONS)
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'easing',
    component: isLicenseExpired ? _components_controls_easing_expired__WEBPACK_IMPORTED_MODULE_3__.EasingExpired : _components_controls_easing__WEBPACK_IMPORTED_MODULE_2__.Easing,
    options: Object.keys(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.EASING_OPTIONS)
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'trigger',
    component: isLicenseExpired ? _components_controls_trigger_expired__WEBPACK_IMPORTED_MODULE_19__.TriggerExpired : _components_controls_trigger__WEBPACK_IMPORTED_MODULE_18__.Trigger,
    options: Object.keys(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.TRIGGER_OPTIONS)
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'start',
    component: isLicenseExpired ? _components_controls_start_expired__WEBPACK_IMPORTED_MODULE_15__.StartExpired : _components_controls_start__WEBPACK_IMPORTED_MODULE_14__.Start
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'end',
    component: isLicenseExpired ? _components_controls_end_expired__WEBPACK_IMPORTED_MODULE_7__.EndExpired : _components_controls_end__WEBPACK_IMPORTED_MODULE_6__.End
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'relativeTo',
    component: isLicenseExpired ? _components_controls_relative_to_expired__WEBPACK_IMPORTED_MODULE_9__.RelativeToExpired : _components_controls_relative_to__WEBPACK_IMPORTED_MODULE_8__.RelativeTo,
    options: Object.keys(_components_controls_relative_to__WEBPACK_IMPORTED_MODULE_8__.RELATIVE_TO_OPTIONS)
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'effect',
    component: isLicenseExpired ? _components_controls_effect_pro_expired__WEBPACK_IMPORTED_MODULE_5__.EffectProExpired : _components_controls_effect_pro__WEBPACK_IMPORTED_MODULE_4__.EffectPro,
    options: Object.keys(_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.EFFECT_OPTIONS)
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'customEffects',
    component: isLicenseExpired ? _components_custom_effects_custom_effects_expired__WEBPACK_IMPORTED_MODULE_21__.CustomEffectExpired : _components_custom_effects_custom_effects__WEBPACK_IMPORTED_MODULE_20__.CustomEffect
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'repeat',
    component: isLicenseExpired ? _components_controls_repeat_expired__WEBPACK_IMPORTED_MODULE_11__.RepeatExpired : _components_controls_repeat__WEBPACK_IMPORTED_MODULE_10__.Repeat
  });
  (0,_elementor_editor_interactions__WEBPACK_IMPORTED_MODULE_0__.registerInteractionsControl)({
    type: 'times',
    component: isLicenseExpired ? _components_controls_times_expired__WEBPACK_IMPORTED_MODULE_17__.TimesExpired : _components_controls_times__WEBPACK_IMPORTED_MODULE_16__.Times
  });
}

/***/ }),

/***/ "react":
/*!**************************!*\
  !*** external ["React"] ***!
  \**************************/
/***/ (function(module) {

module.exports = window["React"];

/***/ }),

/***/ "@elementor/editor-controls":
/*!*************************************************!*\
  !*** external ["elementorV2","editorControls"] ***!
  \*************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorControls"];

/***/ }),

/***/ "@elementor/editor-editing-panel":
/*!*****************************************************!*\
  !*** external ["elementorV2","editorEditingPanel"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorEditingPanel"];

/***/ }),

/***/ "@elementor/editor-interactions":
/*!*****************************************************!*\
  !*** external ["elementorV2","editorInteractions"] ***!
  \*****************************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorInteractions"];

/***/ }),

/***/ "@elementor/editor-props":
/*!**********************************************!*\
  !*** external ["elementorV2","editorProps"] ***!
  \**********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorProps"];

/***/ }),

/***/ "@elementor/editor-ui":
/*!*******************************************!*\
  !*** external ["elementorV2","editorUi"] ***!
  \*******************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["editorUi"];

/***/ }),

/***/ "@elementor/icons":
/*!****************************************!*\
  !*** external ["elementorV2","icons"] ***!
  \****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["icons"];

/***/ }),

/***/ "@elementor/license-api":
/*!*********************************************!*\
  !*** external ["elementorV2","licenseApi"] ***!
  \*********************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["licenseApi"];

/***/ }),

/***/ "@elementor/ui":
/*!*************************************!*\
  !*** external ["elementorV2","ui"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["ui"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!*************************************************************************!*\
  !*** ./packages/packages/pro/editor-interactions-extended/src/index.ts ***!
  \*************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   init: function() { return /* reexport safe */ _init__WEBPACK_IMPORTED_MODULE_0__.init; }
/* harmony export */ });
/* harmony import */ var _init__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./init */ "./packages/packages/pro/editor-interactions-extended/src/init.ts");

}();
(window.elementorV2 = window.elementorV2 || {}).editorInteractionsExtended = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorInteractionsExtended?.init?.();