<template>
    <el-drawer ref="drawer" :with-header="false" size="50%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
        <div class="drawer-content">
            <el-tabs tab-position="top">
                <el-tab-pane label="基本信息">
                    <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted"></form-create>
                </el-tab-pane>
            </el-tabs>
        </div>
        <div class="drawer-footer">
            <el-button @click="$refs.drawer.closeDrawer()">取 消</el-button>
            <el-button :loading="btnLoading" type="primary" @click="handleSubmit">保存</el-button>
        </div>
    </el-drawer>
</template>

<script>
import { formRule, formOption } from './editForm'
import { getInfo, create, update } from '@/api/user'
import tree from '@/utils/tree'
import { mapActions } from 'vuex'

export default {
    name: 'UserEdit',
    data() {
        return {
            temp: {},
            model: {},
            formRule: formRule,
            formOption: formOption,
            formVisible: false,
            btnLoading: false,
            modelChange: false
        }
    },
    watch: {
        formVisible: function () {
            this.handleReset()
        },
        'model.form': {
            handler(val) {
                if (typeof val !== 'undefined') {
                    this.modelChange = true
                }
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
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
        handleCreate() {
            this.btnLoading = false
            this.formVisible = true
        },
        handleUpdate(id) {
            this.btnLoading = false
            this.formVisible = true
            getInfo(id).then(response => {
                if (response.code === 0) {
                    delete response.data.password
                    const { data } = response
                    this.temp = data
                    // 有点小问题，选了两个，但只复现了一个。
                    const role = data.role_id && data.role_id.split(',').map(item => Number(item)) || []
                    /* role.forEach(item => {
                      let checked = tree.getParentsId(this.nodeList, item)
                      if (checked.length > 0) {
                        parentChecked = parentChecked.push(checked.filter(item => item > 0))
                      } else {
                        parentChecked.push([data.role_id])
                      }
                    })*/
                    this.model.setValue(this.temp)
                    this.model.setValue('role', role.map(item => [item]))
                }
            })
        },
        handleSubmit() {
            const success = (response) => {
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
                this.formLoading = false
                this.btnLoading = false
            }
            const error = (error) => {
                this.$message.error(error.message)
                this.formLoading = false
                this.btnLoading = false
            }
            this.model.submit(formData => {
                this.formLoading = true
                this.btnLoading = true
                if(formData.role && formData.role.length > 0) {
                    formData.role_id = formData.role.map(item => item.pop()).join(',')
                }
                delete formData.role
                if (this.temp.id) {
                    formData.id = this.temp.id
                    update(formData).then(success).catch(error)
                } else {
                    create(formData).then(success).catch(error)
                }
            })
        },
        ...mapActions({
            getRole: 'user/getRole',
            getGroup: 'user/getGroup'
        }),
        formMounted() {
            this.getRole().then(response => {
                this.model.mergeRule('role', {
                    props: {
                        options: tree.listToTree(response)
                    }
                })
            })
            this.getGroup().then(response => {
                this.model.mergeRule('group_id', {
                    options: response
                })
            })
        }
    }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>

</style>
