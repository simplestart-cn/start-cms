export const formOption = {
  form: {
    showMessage: true,
    inlineMessage: false,
    statusIcon: true,
    validateOnRuleChange: true,
    labelWidth: "100px",
    size: "small"
  },
  row: { gutter: 10 },
  submitBtn: false,
  resetBtn: false
};

export const formRule = [
  {
    type: "input",
    title: "名称",
    field: "title",
    props: {
      placeholder: "名称"
    },
    validate: [
      {
        required: true,
        message: "名称不能为空"
      }
    ]
  },
  {
    type: "radio",
    title: "状态",
    field: "status",
    value: 1,
    options: [
      { value: 1, label: "正常" },
      { value: 0, label: "禁用" }
    ]
  },
  {
    type: "tree",
    title: "权限",
    field: "auth_tree",
    value: [],
    props: {
      data: [],
      nodeKey: "name",
      defaultExpandAll: true,
      showCheckbox: true,
      checkOnClickNode: true,
      indent: 10,
      props: {
        label: "title",
        children: "children"
      }
    },
    emit: ["check"],
    emitPrefix: "tree"
  }
];
