<template>
    <el-drawer ref="drawer" :with-header="false" size="60%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
        <div class="drawer-content">
            <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted" @tree-check="handleChecked"></form-create>
            <div class="drawer-footer">
                <el-row type="flex" justify="center">
                    <el-button v-waves @click="handleClose">取 消</el-button>
                    <el-button v-waves :loading="btnLoading" type="primary" @click="handleSubmit" v-auth="['role_create', 'role_update']">保存</el-button>
                </el-row>
            </div>
        </div>
    </el-drawer>
</template>

<script>
import { getInfo, create, update } from "@/api/role";
import tree from "@/utils/tree";
import { formOption, formRule } from "./editForm";
import { mapActions } from "vuex";

export default {
    name: "Edit",
    components: {},
    props: {
        parents: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            temp: {},
            model: {},
            formRule: formRule,
            formOption: formOption,
            btnLoading: false,
            modelChange: false,
            formVisible: false,
            parentTop: [{ id: 0, title: "顶级" }]
        };
    },
    computed: {
        parentTree() {
            return this.parentTop.concat(tree.listToTree(this.parents));
        }
    },
    watch: {
        "model.form": {
            handler(val) {
                if (typeof val !== "undefined") {
                    this.modelChange = true;
                }
            },
            immediate: true,
            deep: true
        },
        formVisible: function () {
            this.handleReset();
        },
    },
    created() { },
    methods: {
        handleClose(done) {
            if (this.btnLoading) {
                return;
            }
            if (this.modelChange) {
                this.$confirm("更改将不会被保存，确定要取消吗？")
                    .then((_) => {
                        this.formVisible = false;
                    })
                    .catch((_) => { });
            } else {
                this.formVisible = false;
            }
        },
        handleReset() {
            this.temp = {};
            this.model.form && this.model.resetFields();
            this.modelChange = false;
            this.$nextTick(() => {
                this.model.mergeRule("pid", {
                    props: {
                        options: this.parentTree
                    }
                });
            });
        },
        handleCreate() {
            this.btnLoading = false;
            this.formVisible = true;
        },
        handleUpdate(id) {
            this.formVisible = true;
            getInfo(id).then((response) => {
                if (response.code === 0) {
                    this.temp = response.data;
                    const auths = [];
                    response.data.auth.forEach((item) => {
                        if (!item.half) {
                            auths.push(item.name);
                        }
                    });
                    let parentChecked = tree.getParentsId(this.parents, id);
                    if (parentChecked.length > 0) {
                        parentChecked = parentChecked.filter((item) => item > 0);
                    } else {
                        parentChecked = [];
                    }
                    this.model.setValue(this.temp);
                    this.model.setValue("auth_tree", auths);
                    this.model.setValue(this.temp);
                }
            });
        },
        handleSubmit() {
            this.model.submit((formData) => {
                if (this.temp.id) {
                    formData.id = this.temp.id;
                }
                if (formData.pid && Array.isArray(formData.pid)) {
                    formData.pid = formData.pid.pop();
                }
                formData.auth = JSON.stringify(this.temp.auth);
                delete formData.auth_tree;
                this.btnLoading = true;
                if (formData.id) {
                    update(formData).then(response => {
                        this.btnLoading = false;
                        if (!response.code) {
                            if (!this.temp.id) {
                                this.temp.id = response.data.id;
                            }
                            this.$emit("updateRow", this.temp);
                            this.formVisible = false;
                            this.$message.success(response.msg);
                        } else {
                            this.$message.error(response.msg);
                        }
                    })
                } else {
                    create(formData).then(response => {
                        this.btnLoading = false;
                        if (!response.code) {
                            if (!this.temp.id) {
                                this.temp.id = response.data.id;
                            }
                            this.$emit("updateRow", this.temp);
                            this.formVisible = false;
                            this.$message.success(response.msg);
                        } else {
                            this.$message.error(response.msg);
                        }
                    })
                }
            });
        },
        handleChecked(data, tree) {
            const halfChecked = tree.halfCheckedKeys.map((item) => {
                return { half: 1, name: item };
            });
            const checked = tree.checkedKeys.map((item) => {
                return { half: 0, name: item };
            });
            this.temp.auth = halfChecked.concat(checked);
        },
        ...mapActions({
            getAuth: "user/getAuth"
        }),
        formMounted() {
            this.getAuth().then((response) => {
                this.model.mergeRule("auth_tree", {
                    props: {
                        data: tree.listToTree(response)
                    },
                });
            });
        }
    }
};
</script>
<style type="text/scss" lang="scss" scope>
</style>
