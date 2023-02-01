export const formOption = {
  form: {
    showMessage: true,
    inlineMessage: false,
    statusIcon: true,
    validateOnRuleChange: true,
    labelWidth: '150px',
    size: 'small'
  },
  row: {
    gutter: 10
  },
  submitBtn: false,
  resetBtn: false
}
export const formRule = [
  {
    type: 'select',
    field: 'group',
    title: '配置分组',
    value: '',
    options: [],
    props: {
      filterable: true,
      allowCreate: true,
      defaultFirstOption: true
    },
    validate: [{
      message: '请选择分组'
    }]
  }, {
    type: 'input',
    title: '配置名称',
    field: 'title',
    value: '',
    props: {
      placeholder: '请输入标题'
    },
    validate: [{
      required: true,
      message: '标题不能为空'
    }]
  }, {
    type: 'input',
    title: '字段名称',
    field: 'field',
    value: '',
    props: {
      placeholder: '字段名仅限英文且确保唯一'
    },
    validate: [{
      required: true,
      message: '字段不能为空'
    }]
  }, {
    type: 'input',
    title: '默认值',
    field: 'default',
    value: '',
    props: {
      placeholder: '配置默认值'
    }
  }, {
    type: "switch",
    title: "锁定",
    field: "is_locking",
    value: false,
    col: { span: 6 },
    control:[{
      value: true,
      rule:[{
        type: 'el-col',
        props: {span: 18 },
        children: ['锁定后无法编辑删除该字段']
      }]
    }]
  },{
    type: "select",
    field: "type",
    title: "组件类型",
    value: [],
    options: [{
      "value": "switch",
      "label": "开关"
    }, {
      "value": "input",
      "label": "输入框"
    }, {
      "value": "radio",
      "label": "单选框"
    }, {
      "value": "checkbox",
      "label": "多选框"
    }, {
      "value": "select",
      "label": "下拉框"
    }, {
      "value": "DatePicker",
      "label": "日期选择器"
    }, {
      "value": "TimePicker",
      "label": "时间选择器"
    }, {
      "value": "ColorPicker",
      "label": "颜色选择器"
    }, {
      "value": "slider",
      "label": "滑块选择器"
    }, {
      value: 'file-library',
      label: '文件库'
    }],
    validate: [{
      required: true,
      message: '请选择类型'
    }],
    control: [{
      value: 'switch',
      rule: [{
        type: "switch",
        title: "组件示例",
        field: "switch_demo",
      }]
    }, {
      value: 'input',
      rule: [{
        type: "radio",
        title: "输入类型",
        field: "props_type",
        value: "text",
        options: [{
          value: "text",
          label: "单行"
        }, {
          value: "textarea",
          label: "多行"
        }, {
          value: "number",
          label: "数字"
        }, {
          value: "password",
          label: "密码"
        }, ],
      }, {
        type: 'input',
        title: '提示信息',
        field: 'props_placeholder',
        value: '',
        props: {
          placeholder: '输入框提示信息'
        }
      }]
    }, {
      value: 'select',
      rule: [{
        type: "switch",
        title: "可多选",
        field: "props_multiple",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "switch",
        title: "可搜索",
        field: "props_filterable",
        value: false,
        col: {
          span: 6
        },
      }, {
        type: "switch",
        title: "可清空选项",
        field: "props_clearable",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: 'input',
        title: '提示信息',
        field: 'props_placeholder',
        value: '',
        props: {
          placeholder: '选择提示信息'
        }
      }, {
        type: 'group',
        title: '可选项',
        field: 'options',
        value: [],
        props: {
          min: 1,
          max: 30,
          expand: 1,
          button: true,
          rules: [{
            type: 'input',
            title: '',
            field: 'label',
            value: '',
            col: {
              span: 12
            },
            props: {
              placeholder: '选项名称,如：蓝色'
            },
            validate: [{
              required: true,
              message: '名称不能为空'
            }]
          }, {
            type: 'input',
            title: '',
            field: 'value',
            col: {
              span: 12
            },
            props: {
              placeholder: '选项值,如：blue'
            },
            validate: [{
              required: true,
              message: '值不能为空'
            }]
          }]
        },
        validate: [{
          required: true,
          type: 'array',
          min: 1,
          message: '最少添加1个可选项'
        }],
      }]
    }, {
      handle: function (val, $f) {
        return ['radio', 'checkbox', 'select'].indexOf(val) > -1
      },
      rule: [{
        type: 'group',
        title: '可选项',
        field: 'options',
        value: [],
        props: {
          min: 1,
          max: 30,
          expand: 1,
          button: true,
          rules: [{
            type: 'input',
            title: '',
            field: 'label',
            value: '',
            col: {
              span: 12
            },
            props: {
              placeholder: '选项名称,如：蓝色'
            },
            validate: [{
              required: true,
              message: '名称不能为空'
            }]
          }, {
            type: 'input',
            title: '',
            field: 'value',
            col: {
              span: 12
            },
            props: {
              placeholder: '选项值,如：blue'
            },
            validate: [{
              required: true,
              message: '值不能为空'
            }]
          }]
        },
        validate: [{
          required: true,
          type: 'array',
          min: 1,
          message: '最少添加1个可选项'
        }],
      }]
    }, {
      value: 'DatePicker',
      rule: [{
        type: "switch",
        title: "可输入",
        field: "props_editable",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "switch",
        title: "可清空",
        field: "props_clearable",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "radio",
        title: "显示类型",
        field: "props_type",
        value: "date",
        options: [{
          value: "year",
          label: "年份"
        }, {
          value: "month",
          label: "月份"
        }, {
          value: "date",
          label: "日期"
        }, {
          value: "dates",
          label: "多个日期"
        }, {
          value: "daterange",
          label: "日期范围"
        }, {
          value: "datetimerange",
          label: "日期时间范围"
        }, ],
        control: [{
          handle: function (val, $f) {
            return ['daterange', 'datetimerange'].indexOf(val) > -1
          },
          rule: [{
            type: 'input',
            title: '提示信息',
            field: 'props_startPlaceholder',
            value: '',
            props: {
              placeholder: '开始日期提示信息'
            }
          }, {
            type: 'input',
            title: '提示信息',
            field: 'props_endPlaceholder',
            value: '',
            props: {
              placeholder: '结束日期提示信息'
            }
          }]
        }]
      }, {
        type: 'input',
        title: '显示格式',
        field: 'props_format',
        value: 'yyyy-MM-dd',
        props: {
          placeholder: '仅选年/月/日时，格式填写YYYY或MM或dd即可'
        }
      }, {
        type: 'input',
        title: '提示信息',
        field: 'props_placeholder',
        value: '',
        props: {
          placeholder: '非范围选择时提示'
        }
      }]
    }, {
      value: 'TimePicker',
      rule: [{
        type: "switch",
        title: "范围选择",
        field: "props_isRange",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "switch",
        title: "可输入",
        field: "props_editable",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "switch",
        title: "可清空",
        field: "props_clearable",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: 'input',
        title: '提示信息',
        field: 'props_placeholder',
        value: '',
        props: {
          placeholder: '非范围选择时提示'
        }
      }]
    }, {
      value: 'ColorPicker',
      rule: [{
        type: "switch",
        title: "透明度",
        field: "props_showAlpha",
        value: false,
        col: {
          span: 6
        },
      }, {
        type: "radio",
        title: "数据格式",
        field: "props_colorFormat",
        value: "rgb",
        options: [{
          value: "rgb",
          label: "RGB"
        }, {
          value: "hex",
          label: "HEX"
        }, {
          value: "hls",
          label: "HLS"
        }, {
          value: "hsv",
          label: "HSV"
        }]
      }]
    }, {
      value: 'slider',
      rule: [{
        type: 'inputNumber',
        title: '最小值',
        field: 'props_min',
        value: 0,
        col: {
          span: 8
        },
      }, {
        type: 'inputNumber',
        title: '最大值',
        field: 'props_max',
        value: 100,
        col: {
          span: 8
        },
      }, {
        type: 'inputNumber',
        title: '步长',
        field: 'props_step',
        value: 1,
        col: {
          span: 8
        },
      }, {
        type: "switch",
        title: "范围选择",
        field: "props_range",
        value: false,
        col: {
          span: 6
        }
      }, {
        type: "switch",
        title: "显示输入框",
        field: "props_showInput",
        value: false,
        col: {
          span: 6
        }
      }]
    }, {
      value: 'file-library',
      rule: [{
        type: 'inputNumber',
        title: '最大可上传数',
        field: 'props_limit',
        value: 5
      }, {
        type: 'radio',
        title: '返回参数类型',
        field: 'props_returnIds',
        value: false,
        options: [{
          value: true,
          label: 'ID列表的字符串'
        }, {
          value: false,
          label: '文件路径列表'
        }]
      }]
    }]
  },
  {
    type: 'group',
    title: '校验规则',
    value: [],
    field: 'validate',
    props: {
      max: 1,
      rules: [
        {
          type: 'switch',
          field: 'required',
          title: '是否必填',
          value: true,
          col: { span: 12, labelWidth: '80px' }
        },
        {
          type: 'select',
          field: 'type',
          title: '类型',
          value: 'string',
          options: [
            {
              value: 'string',
              label: '字符串'
            },
            {
              value: 'number',
              label: '数字'
            },
            {
              value: 'boolean',
              label: '布尔值'
            },
            {
              value: 'array',
              label: '数组'
            }
          ],
          col: { span: 12, labelWidth: '80px' }
        },
        {
          type: 'input',
          field: 'pattern',
          title: '正则规则',
          props: {
            placeholder: '请填写合格正确规则/^\\w$/'
          },
          col: { span: 12, labelWidth: '80px' }
        },
        {
          type: 'input',
          field: 'message',
          title: '校验文案',
          col: { span: 12, labelWidth: '80px' },
          props: {
            placeholder: '不满足校验条件，显示的文字'
          }
        }
      ]
    }
  }
]
