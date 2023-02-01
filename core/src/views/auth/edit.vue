<template>
    <el-drawer ref="drawer" :with-header="false" size="50%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
        <div class="drawer-content">
            <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted" @parent-change="handleChange"></form-create>
            <div class="drawer-footer">
                <el-row type="flex" justify="center">
                    <el-button v-waves @click="handleClose">取 消</el-button>
                    <el-button v-waves :loading="btnLoading" type="primary" @click="handleSubmit">保存</el-button>
                </el-row>
            </div>
        </div>
    </el-drawer>
</template>

<script>
import { getInfo, create, update } from '@/api/auth'
import tree from '@/utils/tree'
import { formOption, formRule } from './editForm'
import { mapActions } from 'vuex'
export default {
    name: 'AuthForm',
    components: {},
    props: {
        nodeList: {
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
            nodeTop: [{ 'id': 0, 'title': '顶级' }]
        }
    },
    computed: {
        getNodeTree() {
            return this.nodeTop.concat(tree.listToTree(this.nodeList))
        }
    },
    watch: {
        'model.form': {
            handler(val) {
                if (typeof val !== 'undefined') {
                    this.modelChange = true
                }
            },
            immediate: true,
            deep: true
        },
        formVisible: function () {
            this.handleReset()
        }
    },
    created() {
    },
    methods: {
        ...mapActions({
            getList: 'app/getList'
        }),
        formMounted() {
            this.getList().then(response => {
                this.model.mergeRule('app', {
                    options: response
                })
            })
            this.model.mergeRule('parent', {
                props: {
                    options: this.getNodeTree
                }
            })
        },
        handleClose(done) {
            if (this.btnLoading) {
                return
            }
            if (this.modelChange) {
                this.$confirm('更改将不会被保存，确定要取消吗？')
                    .then(_ => {
                        this.formVisible = false
                    })
                    .catch(_ => { })
            } else {
                this.formVisible = false
            }
        },
        handleReset() {
            this.temp = {}
            this.model.form && this.model.resetFields()
            this.modelChange = false
        },
        handleCreate(pid) {
            this.btnLoading = false
            this.formVisible = true
        },
        handleUpdate(id) {
            this.formVisible = true
            getInfo(id).then(response => {
                if (response.code === 0) {
                    this.temp = response.data
                    this.model.setValue(this.temp)
                    let parentChecked = tree.getParentsId(this.nodeList, id)
                    if (parentChecked && parentChecked.length > 1) {
                        parentChecked = parentChecked.filter(item => item > 0)
                        this.model.setValue('parent', parentChecked)
                    }
                }
            })
        },
        handleSubmit() {
            const success = response => {
                if (response.code === 0) {
                    if (!this.temp.id) {
                        this.temp.id = response.data.id
                    }
                    this.$emit('updateRow', this.temp)
                    this.formVisible = false
                    this.$message.success(response.msg)
                } else {
                    this.$message.error(response.msg)
                }
                this.btnLoading = false
            }
            const error = (error) => {
                this.$message.error(error.message)
                this.formLoading = false
                this.btnLoading = false
            }
            this.model.submit(formData => {
                this.btnLoading = true
                delete formData.parent
                Object.assign(this.temp, formData)
                if (!this.temp.id) {
                    create(this.temp).then(success).catch(error)
                } else {
                    update(this.temp).then(success).catch(error)
                }
            })
        },
        handleChange(value) {
            if (value && value.length) {
                this.temp.pid = value[value.length - 1]
            }
        }
    }
}
</script>
<style type="text/scss" lang="scss" scoped>
</style>
