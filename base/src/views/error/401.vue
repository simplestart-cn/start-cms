<template>
    <div class="err-page">
      <div class="err-page-container">
        <el-row>
            <el-col :span="12">
                <h1 class="text-jumbo text-ginormous">OOPS!</h1>
                <h2>你没有权限去该页面</h2>
                <h3>如有不满请联系你领导</h3>
                <el-button icon="el-icon-arrow-left" class="pan-back-btn" @click="back">返回</el-button>
            </el-col>
            <el-col :span="12">
                <img :src="errGif" width="313" height="428" alt="Girl has dropped her ice cream.">
            </el-col>
        </el-row>
        <div class="copyright">Powered by <a href="http://www.startcms.cn" target="_blank">start-cms</a></div>
    </div>
    </div>
</template>

<script>
import errGif from '@/assets/401_images/401.gif'

export default {
    name: 'Page401',
    data() {
        return {
            errGif: errGif + '?' + +new Date(),
        }
    },
    methods: {
        back() {
            if (this.$route.query.noGoBack) {
                this.$router.push({ path: '/dashboard' })
            } else {
                this.$router.go(-1)
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.err-page {
  padding-top: 100px;
}
.err-page-container {
    width: 800px;
    max-width: 100%;
    margin: 0 auto;
    .pan-back-btn {
        background: #008489;
        color: #fff;
        border: none !important;
    }
    .pan-gif {
        margin: 0 auto;
        display: block;
    }
    .pan-img {
        display: block;
        margin: 0 auto;
        width: 100%;
    }
    .text-jumbo {
        font-size: 60px;
        font-weight: 700;
        color: #484848;
    }
    .list-unstyled {
        font-size: 14px;
        li {
            padding-bottom: 5px;
        }
        a {
            color: #008489;
            text-decoration: none;
            &:hover {
                text-decoration: underline;
            }
        }
    }
}
.copyright {
    position: fixed;
    right: 30px;
    bottom: 30px;
    color: #666;
    font-size: 14px;
    text-align: center;
    a {
        text-decoration: underline;
    }
}
</style>
