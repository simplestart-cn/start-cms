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
    type: 'radio',
    title: '调试模式',
    field: 'debug',
    value: 1,
    options: [
      { value: 1, label: '开启' },
      { value: 0, label: '关闭' }
    ]
  },
  {
    type: 'input',
    title: '调试入口',
    field: 'dev_entry',
    props: {
      placeholder: '调试入口'
    },
  },
]
