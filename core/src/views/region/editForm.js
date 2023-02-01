export const formOption = {
  form: {
    showMessage: true,
    inlineMessage: false,
    statusIcon: true,
    validateOnRuleChange: true,
    labelWidth: '100px',
    size: 'small'
  },
  row: { gutter: 10 },
  submitBtn: false,
  resetBtn: false
}

export const formRule = [
  {
    type: 'cascader',
    title: '上级',
    field: 'parent',
    props: {},
    emit: ['change'],
    emitPrefix: 'parent'
  }, {
    type: 'input',
    title: '名称',
    field: 'title',
    props: {
      placeholder: '地区名称'
    },
    validate: [{
      required: true,
      message: '地区名称不能为空'
    }]
  }, {
    type: 'input',
    title: '缩写',
    field: 'short_title',
    props: {
      placeholder: '地区缩写'
    }
  }, {
    type: 'input',
    title: '全称',
    field: 'merger_name',
    props: {
      placeholder: '地区全称'
    }
  }, {
    type: 'input',
    title: '拼音',
    field: 'pinyin',
    props: {
      placeholder: '地区拼音'
    }
  }, {
    type: 'input',
    title: '手写字母',
    field: 'first',
    props: {
      placeholder: '手写字母'
    }
  }, {
    type: 'radio',
    title: '状态',
    field: 'status',
    value: 1,
    options: [
      { value: 1, label: '正常' },
      { value: 0, label: '禁用' }
    ]
  }
]
