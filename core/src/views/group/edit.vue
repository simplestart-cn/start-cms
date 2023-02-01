<template>
    <el-drawer ref="drawer" :with-header="false" size="50%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
        <div class="drawer-content">
            <form-create v-model="model" :rule="formRule" :option="formOption"></form-create>
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
import { getInfo, create, update } from '@/api/group'
import { formOption, formRule } from './editForm'

export default {
    name: 'Edit',
    components: {},
    data() {
        return {
            temp: {},
            model: {},
            formRule: formRule,
            formOption: formOption,
            btnLoading: false,
            modelChange: false,
            formVisible: false
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
            this.formVisible = true
            const _this = this
            getInfo(id).then(response => {
                if (response.code === 0) {
                    _this.temp = response.data
                    this.model.setValue(_this.temp)
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
                this.btnLoading = false
                if (!this.temp.id) {
                    create(this.temp).then(success).catch(error)
                } else {
                    update(this.temp).then(success).catch(error)
                }
            })
        }
    }
}
</script>
<style type="text/scss" lang="scss" scoped>

</style>