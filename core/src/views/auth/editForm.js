export const formOption = {
    form: {
        showMessage: true,
        inlineMessage: false,
        statusIcon: true,
        validateOnRuleChange: true,
        labelWidth: "100px",
        size: "small",
    },
    row: { gutter: 10 },
    submitBtn: false,
    resetBtn: false,
};

export const formRule = [
    {
        type: "cascader",
        title: "上级",
        field: "parent",
        props: {
            options: [],
            placeholder: "请选择",
            clearable: true,
            props: {
                label: "title",
                value: "id",
                children: "children",
                // form-create上写changeOnSelect用来选择任意一级无效，具体看element_ui决定
                checkStrictly: true,
            },
        },
        emit: ["change"],
        emitPrefix: "parent",
    },
    {
        type: "select",
        title: "所属模块",
        field: "app",
        props: {
            placeholder: "所属模块信息",
        },
        validate: [
            {
                required: true,
                message: "模块信息不能为空",
            },
        ],
    },
    {
        type: "input",
        title: "名称",
        field: "title",
        props: {
            placeholder: "名称",
        },
        validate: [
            {
                required: true,
                message: "名称不能为空",
            },
        ],
    },
    {
        type: "input",
        title: "标识",
        field: "name",
        props: {
            placeholder: "一般以下划线拼接，如：user_info",
        },
        validate: [
            {
                required: true,
                message: "标识不能为空",
            },
        ],
    },
    {
        type: "input",
        title: "节点",
        field: "node",
        props: {
            placeholder: "一般填写接口地址，如：user/info",
        },
        validate: [
            {
                required: true,
                message: "节点不能为空",
            },
        ],
    },
    {
        type: "input",
        title: "路径",
        field: "path",
        props: {
            placeholder: "路径",
        },
    },
    {
        type: "input",
        title: "图标",
        field: "icon",
    },
    {
        type: "input",
        title: "模板",
        field: "view",
        props: {
            placeholder: "渲染模板地址",
        },
    },
    {
        type: "input",
        title: "跳转",
        field: "redirect",
        props: {
            placeholder: "Url跳转地址",
        },
    },
    {
        type: "radio",
        title: "状态",
        field: "status",
        value: 1,
        options: [
            { value: 1, label: "正常" },
            { value: 0, label: "禁用" },
        ],
    },
    {
        type: "radio",
        title: "缓存",
        field: "cache",
        value: 1,
        options: [
            { value: 1, label: "是" },
            { value: 0, label: "否" },
        ],
    },
    {
        type: "radio",
        title: "管理员访问",
        field: "admin",
        value: 0,
        options: [
            { value: 1, label: "是" },
            { value: 0, label: "否" },
        ],
    },
    {
        type: "radio",
        title: "超管员访问",
        field: "super",
        value: 0,
        options: [
            { value: 1, label: "是" },
            { value: 0, label: "否" },
        ],
    },
    {
        type: "radio",
        title: "菜单显示",
        field: "menu",
        value: 0,
        options: [
            { value: 1, label: "是" },
            { value: 0, label: "否" },
        ],
    },
    {
        type: "radio",
        title: "前端路由",
        field: "route",
        value: 0,
        options: [
            { value: 1, label: "是" },
            { value: 0, label: "否" },
        ],
    },
    {
        type: "inputNumber",
        title: "排序",
        field: "sort",
        value: 100,
    },
];
