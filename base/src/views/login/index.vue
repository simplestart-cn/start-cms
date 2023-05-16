<template>
    <div class="login-container" :style="{backgroundImage: backgroundImage}">
        <div class="bubbles" v-if="!backgroundImage">
            <svg v-for="(item, index) in backgroundBubbles" :key="index" version="1.1" id="StartCMS" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="128px" height="128px" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                <path fill="#71C0E9" fill-opacity="0.35" d="M97.658,48.586c0.109,0.193,0.169,0.412,0.169,0.635v33.655c0,0.915-0.489,1.761-1.284,2.216l-29.363,16.84
	c-0.611,0.352-1.393,0.139-1.744-0.473c-0.109-0.193-0.168-0.412-0.168-0.636V67.169c0-0.915,0.489-1.761,1.283-2.216l29.363-16.84
	C96.525,47.763,97.307,47.974,97.658,48.586L97.658,48.586z" />
                <path fill="#71C0E9" fill-opacity="0.35" d="M30.932,47.944c0.223,0,0.442,0.058,0.635,0.169l29.364,16.84c0.794,0.455,1.283,1.301,1.283,2.216v33.654
	c0,0.706-0.571,1.276-1.277,1.276c-0.223,0-0.442-0.059-0.635-0.168L30.939,85.091c-0.794-0.455-1.284-1.301-1.284-2.216V49.221
	C29.655,48.516,30.227,47.944,30.932,47.944L30.932,47.944z" />
                <path fill="#71C0E9" fill-opacity="0.35" d="M65.646,26.805l28.717,16.469c0.611,0.351,0.822,1.131,0.473,1.743c-0.113,0.197-0.275,0.36-0.473,0.472
	L65.646,61.958c-1.182,0.677-2.631,0.677-3.813,0L33.119,45.489c-0.612-0.351-0.823-1.131-0.472-1.743
	c0.113-0.197,0.276-0.36,0.472-0.472l28.717-16.468C63.016,26.128,64.466,26.128,65.646,26.805L65.646,26.805z" />
                <path fill="#BFBFBF" fill-opacity="0.35" d="M63.866,7.875c2.973,0,5.407,2.3,5.618,5.216l35.623,20.543c0.838-0.468,1.783-0.713,2.742-0.711
	c3.111,0,5.633,2.519,5.633,5.625c0,2.206-1.271,4.115-3.121,5.037v40.569c1.994,0.865,3.39,2.851,3.39,5.16
	c0,3.107-2.521,5.627-5.633,5.627c-1.334,0.002-2.627-0.473-3.644-1.338L69.455,113.8c0.027,0.229,0.043,0.464,0.043,0.699
	c0,3.106-2.521,5.626-5.632,5.626s-5.632-2.52-5.632-5.626c0-0.263,0.018-0.522,0.053-0.776L23.833,93.855
	c-1.003,0.924-2.345,1.487-3.817,1.487c-3.11,0-5.632-2.518-5.632-5.625c0-2.209,1.274-4.12,3.129-5.041v-41.29
	c-1.927-0.894-3.263-2.844-3.263-5.105c0-3.107,2.521-5.626,5.632-5.626c1.12,0,2.163,0.327,3.041,0.89l35.32-20.369
	C58.411,10.221,60.864,7.875,63.866,7.875z M68.823,16.174c-0.952,1.758-2.815,2.953-4.958,2.953c-2.116,0-3.96-1.167-4.922-2.891
	l-33.988,19.6c0.369,0.763,0.56,1.599,0.559,2.446c0,2.894-2.188,5.277-5.001,5.591v40.242c2.878,0.251,5.135,2.664,5.135,5.604
	c0,0.551-0.08,1.084-0.228,1.588l34.071,19.648c1.033-1.27,2.608-2.082,4.374-2.082c1.792,0,3.388,0.836,4.419,2.139l34.495-19.893
	c-0.195-0.581-0.295-1.189-0.295-1.803c0-2.852,2.123-5.207,4.875-5.576V44.155c-2.881-0.248-5.144-2.663-5.144-5.605
	c-0.001-0.95,0.239-1.884,0.698-2.716L68.823,16.174L68.823,16.174z" />
            </svg>
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
            btnText: '获取短信验证码',
            btnLoading: false,
            btnDisabled: false,
            backgroundImage: '',
            backgroundBubbles: new Array(10),
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
        getInfo({ app: 'core' }).then(response => {
            this.core = response.data
            if (this.core.background_image) {
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
                        .then((res) => {
                            this.btnLoading = false
                            if (res.code) {
                                this.$message.error(res.msg)
                                return
                            }
                            this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
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
                        .then((res) => {
                            this.btnLoading = false
                            if (res.code) {
                                this.$message.error(res.msg)
                                return
                            }
                            this.$router.push({ path: this.redirect || '/', query: this.otherQuery })
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

        ::v-deep.el-tabs__item {
            padding: 0 10px;
        }
        ::v-deep.el-tabs__nav-wrap::after {
            display: none;
        }
        ::v-deep.el-form-item {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            background: $light;
            color: #262626;
        }
        ::v-deep.el-input {
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
        ::v-deep.el-button {
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
        background-image: linear-gradient(45deg, #0081ff, #1cbbb4);
        > svg {
            position: absolute;
            bottom: -160px;
            width: 40px;
            height: 40px;
            border-radius: 20px;
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
            }
            &:nth-child(5) {
                left: 70%;
            }
            &:nth-child(6) {
                left: 80%;
                width: 120px;
                height: 120px;
                animation-delay: 3s;
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