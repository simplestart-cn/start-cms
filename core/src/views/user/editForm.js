import { validUsername, validPassword, validPhone, validEmail } from '@/utils/validate'
export const formOption = {
  form: {
    showMessage: true,
    inlineMessage: false,
    statusIcon: true,
    validateOnRuleChange: true,
    labelWidth: '70px',
    size: 'small'
  },
  row: { gutter: 10 },
  submitBtn: false,
  resetBtn: false
}

export const formRule = [
  {
    type: 'file-library',
    title: '头像',
    field: 'avatar',
    value: '',
    props: {
      limit: 1,
      multiple: false,
      accept: 'image/*'
    }
  },
  {
    type: 'input',
    title: '账号',
    field: 'account',
    props: {
      clearable: true
    },
    validate: [
      {
        message: '账号必填',
        trigger: 'blur'
      },
      // {
      //   validator: (rule, value, callback) => {
      //     if (validUsername(value)) {
      //       callback()
      //     } else {
      //       return callback(new Error())
      //     }
      //   },
      //   message: '账号格式是字母，数字，下划线，长度6-18位',
      //   trigger: 'blur'
      // }
    ]
  },
  {
    type: 'input',
    title: '密码',
    field: 'password',
    props: {
      clearable: true
    },
    validate: [{
      validator: (rule, value, callback) => {
        if (!value || validPassword(value)) {
          callback()
        } else {
          return callback(new Error())
        }
      },
      message: '密码格式是字母，数字，下划线，长度6-18位',
      trigger: 'blur'
    }]
  },
  {
    type: 'input',
    title: '重复密码',
    field: 'repassword',
    props: {
      clearable: true
    }
  },
  {
    type: 'input',
    title: '姓名',
    field: 'name',
    props: {
      clearable: true
    }
  },
  {
    type: 'input',
    title: '手机',
    field: 'mobile',
    props: {
      clearable: true
    },
    validate: [{
      validator: (rule, value, callback) => {
        if (!value || validPhone(value)) {
          callback()
        } else {
          return callback(new Error())
        }
      },
      message: '手机号格式错误',
      trigger: 'blur'
    }]
  },
  {
    type: 'input',
    title: '邮箱',
    field: 'email',
    props: {
      clearable: true
    },
    validate: [{
      validator: (rule, value, callback) => {
        if (!value || validEmail(value)) {
          callback()
        } else {
          return callback(new Error())
        }
      },
      message: '邮箱格式错误',
      trigger: 'blur'
    }]
  },
  {
    type: 'cascader',
    title: '角色',
    field: 'role',
    options: [],
    props: {
      placeholder: '请选择角色',
      clearable: true,
      props: {
        value: 'id',
        label: 'title',
        expandTrigger: 'hover',
        multiple: true
      }
    }
  },{
    type: 'select',
    title: '部门',
    field: 'group_id',
    value: '',
    options: [],
    props: {
      placeholder: '请选择部门',
      clearable: true
    }
  },
  {
    type: 'input',
    title: '备注',
    field: 'describe',
    props: {
      clearable: true
    }
  },
  {
    type: 'inputNumber',
    title: '排序',
    field: 'sort',
    value: 100,
    props: {
      min: 0
    }
  },
  {
    type: 'radio',
    title: '状态',
    field: 'status',
    value: 1,
    options: [
      {
        value: 1,
        label: '启用'
      },
      {
        value: 0,
        label: '禁用'
      }
    ]
  }
]
