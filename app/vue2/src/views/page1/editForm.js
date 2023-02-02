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
    type: 'input',
    field: 'title',
    title: '名称',
    props: {
      placeholder: '名称'
    },
    validate: [
      { required: true, message: '名称不能为空' }
    ]
  },
  {
    type: 'input',
    field: 'remark',
    title: '备注',
    props: {
      placeholder: '备注信息'
    }
  },
  {
    type: 'radio',
    title: '状态',
    field: 'status',
    value: 1,
    options: [
      { value: 0, label: '禁用' },
      { value: 1, label: '正常' }
    ]
  }
]