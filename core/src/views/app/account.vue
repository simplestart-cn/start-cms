<template>
    <div class="account">
        <div class="flex align-center margin-right-sm text-center">
            <div>{{store.title}}</div>
            <el-badge :is-dot="store.upgradeable" class="margin-left-xs pointer">{{store.version}}</el-badge>
        </div>
        <div class="profile">
            <div class="flex align-center profile" v-if="store.user && store.user.uuid">
                <div class="margin-right-xs">{{store.user.name}}</div>
                <el-avatar icon="el-icon-user" size="small" :src="store.user.avatar"></el-avatar>
            </div>
            <el-button size="mini" type="primary" class="margin-left" @click="dialogVisible = true" v-else>登录</el-button>
        </div>
        <el-dialog width="420px" center modal :visible.sync="dialogVisible">
            <el-form ref="loginForm" :rules="loginRules" :model="loginForm" class="login-form" autocomplete="on" label-position="left" size="medium">
                <h3 class="title">StarCMS应用中心</h3>
                <el-tabs v-model="formType">
                    <el-tab-pane label="登录" name="login">
                        <el-form-item prop="account">
                            <span class="svg-container">
                                <svg-icon icon-class="user" />
                            </span>
                            <el-input ref="account" v-model="loginForm.account" placeholder="账户名/手机号" name="account" type="text" tabindex="1" autocomplete="on" />
                        </el-form-item>
                        <el-form-item prop="password">
                            <span class="svg-container">
                                <svg-icon icon-class="password" />
                            </span>
                            <el-input :key="passwordType" ref="password" v-model="loginForm.password" :type="passwordType" placeholder="登录密码" name="password" tabindex="2" autocomplete="on" @keyup.enter.native="handleLogin" />
                            <span class="show-pwd" @click="showPwd">
                                <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
                            </span>
                        </el-form-item>
                        <el-form-item prop="code">
                            <span class="svg-container">
                                <svg-icon icon-class="lock" />
                            </span>
                            <el-input ref="captcha" v-model="loginForm.code" placeholder="验证码" name="code" tabindex="3" autocomplete="off" @keyup.enter.native="handleLogin" />
                            <span class="change-code" @click="handleImage">
                                <input type="hidden" v-model="loginForm.uniqid" name="uniqid">
                                <img :src="captcha.image">
                            </span>
                        </el-form-item>
                        <el-button :loading="btnLoading" type="primary" size="medium" style="width:100%;margin-top:30px;" @click.native.prevent="handleLogin">登录</el-button>
                    </el-tab-pane>
                    <el-tab-pane name="register" label="注册">
                        <el-form-item prop="mobile">
                            <span class="svg-container">
                                <i class="el-icon-mobile-phone" />
                            </span>
                            <el-input ref="mobile" v-model="loginForm.mobile" placeholder="手机号码" name="mobile" type="text" size="medium" tabindex="1" autocomplete="on" />
                        </el-form-item>
                        <el-form-item prop="code">
                            <span class="svg-container">
                                <i class="el-icon-message" />
                            </span>
                            <el-input ref="captcha" v-model="loginForm.code" placeholder="验证码" name="code" size="medium" tabindex="2" autocomplete="off" @keyup.enter.native="handleLogin" />
                            <span class="change-code" @click="handleCode">
                                <el-button :disabled="btnDisabled" type="text">{{btnText}}</el-button>
                            </span>
                        </el-form-item>
                        <div class="tips text-right text-df">
                            <span>未注册手机号验证后自动注册</span>
                        </div>
                        <el-button :loading="btnLoading" type="primary" size="medium" style="width:100%;margin-top:30px;" @click.native.prevent="handleRegister">注册/登录</el-button>
                    </el-tab-pane>
                </el-tabs>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
import { loginStore, registerStore, getStore, getStoreCaptcha } from '@/api/app'

export default {
    name: 'Account',
    data() {
        const validateMobile = (rule, value, callback) => {
            if (this.formType === 'register' && !value) {
                callback(new Error('手机号不能为空'))
            } else {
                callback()
            }
        }
        const validateAccount = (rule, value, callback) => {
            if (this.formType === 'login' && !value) {
                callback(new Error('账户不能为空'))
            } else {
                callback()
            }
        }
        const validatePassword = (rule, value, callback) => {
            if (this.formType === 'login' && !value) {
                callback(new Error('密码不能为空'))
            } else {
                callback()
            }
        }
        const validateCode = (rule, value, callback) => {
            if (!value) {
                callback(new Error('验证码不能为空'))
            } else {
                callback()
            }
        }
        return {
            store: {},
            formType: 'login',
            loginForm: {
                mobile: '',
                account: '',
                password: '',
                code: ''
            },
            loginRules: {
                mobile: [{ required: true, trigger: 'blur', validator: validateMobile }],
                account: [{ required: true, trigger: 'blur', validator: validateAccount }],
                password: [{ required: true, trigger: 'blur', validator: validatePassword }],
                code: [{ required: true, trigger: 'blur', validator: validateCode }]
            },
            captcha: {
                image: '',
                uniqid: ''
            },
            btnText: '获取短信验证码',
            btnLoading: false,
            btnDisabled: false,
            passwordType: 'password',
            redirect: undefined,
            dialogVisible: false,
            otherQuery: {}
        }
    },
    watch: {
        dialogVisible(val) {
            if (val) {
                this.handleImage()
            }
        }
    },
    mounted() {
        this.fetchStore()
    },
    methods: {
        showPwd() {
            if (this.passwordType === 'password') {
                this.passwordType = ''
            } else {
                this.passwordType = 'password'
            }
            this.$nextTick(() => {
                this.$refs.password.focus()
            })
        },
        async fetchStore() {
            const { data } = await getStore()
            this.store = data;
        },
        async handleImage() {
            const { code, data, msg } = await getStoreCaptcha({ type: 'image' });
            if (!code) {
                this.captcha = data
                this.loginForm.code = ''
                this.loginForm.uniqid = data.uniqid
            } else {
                this.$message.error(msg)
            }
        },
        async handleCode() {
            if (this.btnDisabled) return false;
            const mobile = this.loginForm.mobile;
            this.btnDisabled = true;
            const { code, msg } = await getStoreCaptcha({ type: 'code', 'mobile': mobile });
            if (!code) {
                let time = 60;
                let inter = setInterval(() => {
                    this.btnText = '重新获取(' + time + ')';
                    time--;
                    if (time === 0) {
                        this.btnText = '获取短信验证码';
                        this.btnDisabled = false;
                        clearInterval(inter);
                    }
                }, 1000);
            } else {
                this.$message.error(msg)
                this.btnDisabled = false
            }
        },
        handleLogin() {
            this.$refs.loginForm.validate(valid => {
                if (valid) {
                    // this.btnLoading = true
                    loginStore(this.loginForm).then((res) => {
                        this.btnLoading = false
                        if (res.code) {
                            this.btnLoading = false
                            this.$message.error(res.msg)
                            return
                        }
                        this.store = res.data;
                        this.dialogVisible = false;
                    })
                        .catch(err => {
                            this.btnLoading = false
                            this.handleImage()
                        })
                } else {
                    return false
                }
            })
        },
        handleRegister() {
            this.$refs.loginForm.validate(valid => {
                if (valid) {
                    this.btnLoading = true
                    registerStore(this.loginForm).then((res) => {
                        this.btnLoading = false
                        if (res.code) {
                            this.$message.error(res.msg)
                            return
                        }
                        this.store = res.data;
                        this.dialogVisible = false;
                    })
                        .catch(err => {
                            this.btnLoading = false
                        })
                } else {
                    return false
                }
            })
        }

    }
}
</script>


<style lang="scss" scoped>
$dark: #222;
$light: #fff;
$dark_gray: #afafaf;
$light_gray: #eee;
.account {
    display: flex;
    justify-content: center;
    align-items: center;
    background-size: cover;
    background-repeat: no-repeat;
    overflow: hidden;
    .profile {
        .el-avatar--small {
            width: 24px;
            height: 24px;
            line-height: 24px;
            font-size: 14px;
        }
    }
    .login-bg {
        position: fixed;
        left: 0;
        top: 0;
        min-width: 100%;
        min-height: 100%;
    }
    .login-form {
        position: relative;
        max-width: 100%;
        padding: 0px 30px 60px;
        margin: 0 auto;
        overflow: hidden;
        background-color: #fff;
        border-radius: 10px;
        .title {
            font-size: 26px;
            color: $dark;
            margin: 0px auto 30px auto;
            text-align: center;
            font-weight: bold;
        }

        /deep/.el-tabs__item {
            padding: 0 10px;
        }
        /deep/.el-tabs__nav-wrap::after {
            display: none;
        }
        /deep/.el-form-item {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background: $light;
            color: #262626;
        }
        /deep/.el-input {
            display: inline-block;
            height: 47px;
            width: 85%;
            input {
                background: transparent;
                border: 0px;
                -webkit-appearance: none;
                border-radius: 0px;
                padding: 12px 5px 12px 15px;
                height: 47px;
                color: $dark;
                caret-color: $dark;
                &:-webkit-autofill {
                    box-shadow: 0 0 0px 1000px $light inset !important;
                    -webkit-text-fill-color: $dark !important;
                }
            }
        }
        /deep/.el-button {
            padding: 15px 20px;
        }
    }

    .tips {
        font-size: 14px;
        color: $dark_gray;
        margin-top: -10px;
    }
    .svg-container {
        padding: 6px 5px 6px 15px;
        color: $dark_gray;
        vertical-align: middle;
        width: 30px;
        display: inline-block;
    }

    .show-pwd {
        position: absolute;
        right: 10px;
        top: 7px;
        font-size: 16px;
        color: $dark_gray;
        cursor: pointer;
        user-select: none;
    }

    .change-code {
        position: absolute;
        right: 1px;
        bottom: 1px;
        cursor: pointer;
        user-select: none;
        line-height: 1;
    }
}
</style>