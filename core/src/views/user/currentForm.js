import { validUsername, validPassword, validPhone, validEmail } from '@/utils/validate'
export const formOption = {
    form: {
        showMessage: true,
        inlineMessage: false,
        statusIcon: true,
        validateOnRuleChange: true,
        labelWidth: '80px',
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
            accept: 'image/*',
            library: false
        }
    },
    {
        type: 'input',
        title: '登录账号',
        field: 'account',
        props: {},
        validate: [{
            required: true,
            message: '登录账户不能为空'
        },{
            validator: (rule, value, callback) => {
                if (validUsername(value)) {
                    callback()
                } else {
                    return callback(new Error())
                }
            },
            message: '账号格式是字母，数字，下划线，长度5-12位',
            trigger: 'blur'
        }]
    },
    {
        type: 'input',
        title: '登录密码',
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
        title: '真实姓名',
        field: 'name',
        props: {
            clearable: true
        }
    },
    {
        type: 'input',
        title: '手机号码',
        field: 'mobile',
        props: {
            disabled: true,
            readonly: true,
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
        title: '邮箱地址',
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
    }
]
