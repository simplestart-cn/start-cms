<template>
    <div class="http403-container">
        <div class="bullshit">
            <div class="bullshit__oops">OOPS!</div>
            <div class="bullshit__headline">你没有权限去该页面</div>
            <div class="bullshit__info">如有不满请联系你领导分配访问权限</div>
            <a href="/" class="bullshit__return-home">返回首页</a>
        </div>
        <div class="copyright">Powered by <a href="http://www.startcms.cn" target="_blank">StartCMS</a></div>
    </div>
</template>

<script>
export default {
    name: 'Page401',
}
</script>

<style lang="scss" scoped>
.http403-container {
    position: relative;
    display: flex;
    width: 100%;
    height: 100%;
    align-items: center;
    justify-content: center;
    .bullshit {
        position: relative;
        float: left;
        width: 300px;
        padding: 30px 0;
        overflow: hidden;
        &__oops {
            font-size: 32px;
            font-weight: bold;
            line-height: 40px;
            color: #1482f0;
            opacity: 0;
            margin-bottom: 20px;
            animation-name: slideUp;
            animation-duration: 0.5s;
            animation-fill-mode: forwards;
        }
        &__headline {
            font-size: 20px;
            line-height: 24px;
            color: #222;
            font-weight: bold;
            opacity: 0;
            margin-bottom: 10px;
            animation-name: slideUp;
            animation-duration: 0.5s;
            animation-delay: 0.1s;
            animation-fill-mode: forwards;
        }
        &__info {
            font-size: 13px;
            line-height: 21px;
            color: grey;
            opacity: 0;
            margin-bottom: 30px;
            animation-name: slideUp;
            animation-duration: 0.5s;
            animation-delay: 0.2s;
            animation-fill-mode: forwards;
        }
        &__return-home {
            display: block;
            float: left;
            width: 110px;
            height: 36px;
            background: #1482f0;
            border-radius: 100px;
            text-align: center;
            color: #ffffff;
            opacity: 0;
            font-size: 14px;
            line-height: 36px;
            cursor: pointer;
            animation-name: slideUp;
            animation-duration: 0.5s;
            animation-delay: 0.3s;
            animation-fill-mode: forwards;
        }
        @keyframes slideUp {
            0% {
                transform: translateY(60px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }
    }
}
.copyright {
    position: fixed;
    right: 30px;
    bottom: 30px;
    color: #666;
    font-size: 12px;
    text-align: center;
    a {
        color: #1482f0;
        text-decoration: underline;
    }
}
</style>
