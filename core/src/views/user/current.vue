<template>
    <div class="drawer-page inline">
        <div class="drawer-content">
            <el-tabs tab-position="top">
                <el-tab-pane label="基本信息">
                    <el-row :gutter="10">
                        <el-col :lg="12" :md="24">
                            <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted"></form-create>
                        </el-col>
                    </el-row>
                </el-tab-pane>
            </el-tabs>
        </div>
        <div class="drawer-footer">
            <el-button :loading="btnLoading" type="primary" @click="handleSubmit">保存</el-button>
        </div>
    </div>
</template>

<script>
import { formRule, formOption } from './currentForm'
import { getCurrent, updateCurrent } from '@/api/user'
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
            btnLoading: false,
            modelChange: false
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
        }
    },
    created() {
        this.fetchCurrent();
    },
    methods: {
        fetchCurrent(id) {
            this.btnLoading = false
            getCurrent(id).then(response => {
                if (response.code === 0) {
                    const { data } = response
                    this.temp = data
                    this.model.setValue(data)
                }
            })
        },
        handleSubmit() {
            // this.btnLoading = true
            const success = (response) => {
                if (response.code === 0) {
                    if (!this.temp.id) {
                        this.temp.id = response.data.id
                    }
                    this.$store.commit('SET_NAME', response.data.name)
                    this.$store.commit('SET_ACCOUNT', response.data.account)
                    this.$store.commit('SET_AVATAR', response.data.avatar)
                    this.$store.commit('SET_MOBILE', response.data.mobile)
                    this.$store.commit('SET_EMAIL', response.data.email)
                    this.$message.success(response.msg)
                } else {
                    this.$message.error(response.msg)
                }
                this.btnLoading = false
            }
            const error = (error) => {
                this.$message.error(error.message)
                this.btnLoading = false
            }
            this.model.submit(formData => {
                updateCurrent(formData).then(success).catch(error)
            })
        },
        formMounted() {

        }
    }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.edit-drawer {
    .drawer-footer {
        position: fixed;
        top: 15px;
        right: 30px;
    }
    .drawer-content .el-tabs {
        width: 100%;
        height: 100vh;
        padding: 20px;
        padding-top: 48px;
        overflow-y: scroll;
    }
}
</style>