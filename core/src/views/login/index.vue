<template>
    <div class="login-container" :style="{backgroundImage: backgroundImage}">
        <div class="bubbles">
            <div v-for="(item, index) in bubbles" :key="index"></div>
        </div>
        <el-form ref="loginForm" :rules="loginRules" :model="loginForm" class="login-form shadow" autocomplete="on" label-position="left" size="medium">
            <h3 class="title" v-if="core.title">{{core.title}}</h3>
            <h3 class="title" style="height: 30px" v-else></h3>
            <el-tabs v-model="formType">
                <el-tab-pane label="密码登录" name="login">
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
                            <input type="hidden" name="uniqid" :value="captcha.uniqid">
                            <img :src="captcha.image">
                        </span>
                    </el-form-item>
                    <el-button :loading="btnLoading" type="primary" size="medium" style="width:100%;margin-top:30px;" @click.native.prevent="handleLogin">登录</el-button>
                </el-tab-pane>
                <el-tab-pane name="register" label="手机登陆" :disabled="!core.open_register">
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
                    <div class="social-login flex align-center justify-center">
                        <!-- 后续开发 -->
                        <!-- <div class="flex align-center margin-lr-sm">
                            <svg class="Zi Zi--WeChat Login-socialIcon" fill="#60c84d" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M2.224 21.667s4.24-1.825 4.788-2.056C15.029 23.141 22 17.714 22 11.898 22 6.984 17.523 3 12 3S2 6.984 2 11.898c0 1.86.64 3.585 1.737 5.013-.274.833-1.513 4.756-1.513 4.756zm5.943-9.707c.69 0 1.25-.569 1.25-1.271a1.26 1.26 0 0 0-1.25-1.271c-.69 0-1.25.569-1.25 1.27 0 .703.56 1.272 1.25 1.272zm7.583 0c.69 0 1.25-.569 1.25-1.271a1.26 1.26 0 0 0-1.25-1.271c-.69 0-1.25.569-1.25 1.27 0 .703.56 1.272 1.25 1.272z" fill-rule="evenodd"></path>
                            </svg>
                            <span>微信</span>
                        </div>
                        <div class="flex align-center margin-lr-sm">
                            <svg class="Zi Zi--QQ Login-socialIcon" fill="#50c8fd" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M12.003 2c-2.265 0-6.29 1.364-6.29 7.325v1.195S3.55 14.96 3.55 17.474c0 .665.17 1.025.281 1.025.114 0 .902-.484 1.748-2.072 0 0-.18 2.197 1.904 3.967 0 0-1.77.495-1.77 1.182 0 .686 4.078.43 6.29 0 2.239.425 6.287.687 6.287 0 0-.688-1.768-1.182-1.768-1.182 2.085-1.77 1.905-3.967 1.905-3.967.845 1.588 1.634 2.072 1.746 2.072.111 0 .283-.36.283-1.025 0-2.514-2.166-6.954-2.166-6.954V9.325C18.29 3.364 14.268 2 12.003 2z" fill-rule="evenodd">
                                </path>
                            </svg>
                            <span>QQ</span>
                        </div>
                        <div class="flex align-center margin-lr-sm">
                            <svg class="Zi Zi--Weibo Login-socialIcon" fill="#fb6622" viewBox="0 0 24 24" width="24" height="24">
                                <path fill="#FB6622" d="M15.518 3.06c8.834-.854 7.395 7.732 7.394 7.731-.625 1.439-1.673.309-1.673.309.596-7.519-5.692-6.329-5.692-6.329-.898-1.067-.029-1.711-.029-1.711zm4.131 6.985c-.661 1.01-1.377.126-1.376.126.205-3.179-2.396-2.598-2.396-2.598-.719-.765-.091-1.346-.091-1.346 4.882-.551 3.863 3.818 3.863 3.818zM5.317 7.519s4.615-3.86 6.443-1.328c0 0 .662 1.08-.111 2.797.003-.003 3.723-1.96 5.408.159 0 0 .848 1.095-.191 2.649 0 0 2.918-.099 2.918 2.715 0 2.811-4.104 6.44-9.315 6.44-5.214 0-8.026-2.092-8.596-3.102 0 0-3.475-4.495 3.444-10.33zm10.448 7.792s.232-4.411-5.71-4.207c-6.652.231-6.579 4.654-6.579 4.654.021.39.097 3.713 5.842 3.713 5.98 0 6.447-4.16 6.447-4.16zm-9.882.86s-.059-3.632 3.804-3.561c3.412.06 3.206 3.165 3.206 3.165s-.026 2.979-3.684 2.979c-3.288 0-3.326-2.583-3.326-2.583zm2.528 1.037c.672 0 1.212-.447 1.212-.998 0-.551-.543-.998-1.212-.998-.672 0-1.215.447-1.215.998 0 .551.546.998 1.215.998z" fill-rule="evenodd">
                                </path>
                            </svg>
                            <span>微博</span>
                        </div> -->
                    </div>
                    <el-button :loading="btnLoading" type="primary" size="medium" style="width:100%;margin-top:30px;" @click.native.prevent="handleRegister">注册/登录</el-button>
                </el-tab-pane>
            </el-tabs>
        </el-form>
        <div class="copyright">Powered by <a href="http://www.startcms.cn" target="_blank">start-cms</a></div>
    </div>
</template>

<script>
import { getInfo } from '@/api/config'
import { getImage, getCode } from '@/api/captcha'

export default {
    name: 'Login',
    components: {},
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
            core: {
                logo: '',
                title: '',
                open_register: false
            },
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
            bubbles: new Array(10),
            btnText: '获取短信验证码',
            btnLoading: false,
            btnDisabled: false,
            backgroundImage: 'linear-gradient(45deg, #0081ff, #1cbbb4)',
            passwordType: 'password',
            redirect: undefined,
            otherQuery: {}
        }
    },
    computed: {

    },
    watch: {
        $route: {
            handler: function (route) {
                const query = route.query
                if (query) {
                    this.redirect = query.redirect
                    this.otherQuery = this.getOtherQuery(query)
                }
            },
            immediate: true
        }
    },
    created() {
        getInfo({app: 'core'}).then(response => {
            this.core = response.data
            if(this.core.background_image){
                this.backgroundImage = 'url(' + this.core.background_image + ')'
            }
        })
        this.handleImage()
    },
    mounted() {
        if (this.loginForm.account === '') {
            this.$refs.account.focus()
        } else if (this.loginForm.password === '') {
            this.$refs.password.focus()
        }
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
        handleImage() {
            getImage().then(res => {
                this.captcha = res.data
                this.loginForm.code = ''
            })
        },
        handleCode() {
            if (this.btnDisabled) return false;
            const mobile = this.loginForm.mobile;
            this.btnDisabled = true;
            getCode({ mobile }).then(res => {
                if (!res.code) {
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
                }
            }).catch(err => {
                this.btnDisabled = false
            })
        },
        handleLogin() {
            this.$refs.loginForm.validate(valid => {
                if (valid) {
                    Object.assign(this.loginForm, this.captcha)
                    this.btnLoading = true
                    this.$store.dispatch('user/login', this.loginForm)
                        .then(() => {
                            this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
                            this.btnLoading = false
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
                    this.$store.dispatch('user/register', this.loginForm)
                        .then(() => {
                            this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
                            this.btnLoading = false
                        })
                        .catch(err => {
                            this.btnLoading = false
                        })
                } else {
                    return false
                }
            })
        },
        getOtherQuery(query) {
            return Object.keys(query).reduce((acc, cur) => {
                if (cur !== 'redirect') {
                    acc[cur] = query[cur]
                }
                return acc
            }, {})
        }

    }
}
</script>


<style lang="scss" scoped>
$dark: #222;
$light: #fff;
$dark_gray: #afafaf;
$light_gray: #eee;
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    width: 100%;
    background-size: cover;
    background-repeat: no-repeat;
    overflow: hidden;
    .login-bg {
        position: fixed;
        left: 0;
        top: 0;
        min-width: 100%;
        min-height: 100%;
    }
    .login-form {
        position: relative;
        width: 420px;
        max-width: 100%;
        padding: 60px 35px;
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
    .social-login {
        height: 65px;
        svg {
            margin-right: 5px;
        }
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
    .copyright {
        position: absolute;
        right: 30px;
        bottom: 30px;
        color: #ffffff;
        font-size: 14px;
        text-align: center;
        a {
            text-decoration: underline;
        }
    }

    .bubbles {
    position: absolute;
    // 使气泡背景充满整个屏幕
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    >div {
        position: absolute;
        bottom: -160px;
        // 默认的气泡大小；
        width: 40px;
        height: 40px;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.15);
        animation: square 15s infinite;
        transition-timing-function: linear;
        &:nth-child(1) {
            left: 10%;
        }
        &:nth-child(2) {
            left: 20%;
            width: 80px;
            height: 80px;
            animation-delay: 2s;
            animation-duration: 7s;
        }
        &:nth-child(3) {
            left: 25%;
            animation-delay: 4s;
        }
        &:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-duration: 8s;
            background-color: rgba(255, 255, 255, 0.3);
        }
        &:nth-child(5) {
            left: 70%;
        }
        &:nth-child(6) {
            left: 80%;
            width: 120px;
            height: 120px;
            animation-delay: 3s;
            background-color: rgba(255, 255, 255, 0.2);
        }
        &:nth-child(7) {
            left: 32%;
            width: 120px;
            height: 120px;
            animation-delay: 2s;
        }
        &:nth-child(8) {
            left: 55%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
            animation-duration: 15s;
        }
        &:nth-child(9) {
            left: 25%;
            width: 10px;
            height: 10px;
            animation-delay: 2s;
            animation-duration: 12s;
            background-color: rgba(255, 255, 255, 0.3);
        }
        &:nth-child(10) {
            left: 70%;
            width: 120px;
            height: 120px;
            animation-delay: 5s;
        }
    }
    // 自定义 square 动画；
    @keyframes square {
        0% {
            opacity: 0.5;
            transform: translateY(0px) rotate(45deg);
        }
        25% {
            opacity: 0.75;
            transform: translateY(-400px) rotate(90deg);
        }
        50% {
            opacity: 1;
            transform: translateY(-600px) rotate(135deg);
        }
        100% {
            opacity: 0;
            transform: translateY(-1000px) rotate(180deg);
        }
    }
}
}
</style>