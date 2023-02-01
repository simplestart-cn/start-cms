<template>
    <div class="hover flex justify-bewteen align-center bg-white padding">
        <div class="flex align-center flex-auto">
            <el-avatar :src="user.avatar" v-if="user.avatar"></el-avatar>
            <el-avatar icon="el-icon-user-solid" v-else></el-avatar>
            <div class="margin-left-sm">
                <div class="margin-bottom-xs text-black text-xl">{{hello}}{{user.name}} </div>
                <div class="text-gray">{{welcome}}</div>
            </div>
        </div>
        <div class="flex justify-around align-center flex-shrink margin-right-lg">
            <div class="nav-item">
                <i class="el-icon-finished text-blue" />
                <div class="margin-top-xs text-sm">待办(10)</div>
            </div>
            <div class="nav-item">
                <i class="el-icon-message text-green" />
                <div class="margin-top-xs text-sm">通知(20)</div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: 'Dashboard',
    components: {},
    data() {
        return {

        }
    },
    computed: {
        ...mapGetters([
            'user'
        ]),
        hello() {
            const hour = new Date().getHours()
            if (hour < 6) return '早上好,';
            if (hour < 12) return '上午好，';
            if (hour < 14) return '中午好，';
            if (hour < 18) return '下午好，';
            if (hour < 21) return '晚上好，';
            if (hour < 24) return '夜里好，';
            return '你好，';
        },
        welcome() {
            const week = new Array("日", "一", "二", "三", "四", "五", "六");
            const index = new Date().getDay();
            return "今天星期" + week[index] + '，又是美好的一天！';
        }
    },
    mounted() {

    }
}
</script>
<style lang="scss" scoped>
.hover:hover {
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
}
.el-avatar {
    width: 60px;
    height: 60px;
    font-size: 30px;
    line-height: 60px;
}
.nav-item {
    margin-right: 30px;
    text-align: center;
    i {
        font-size: 32px;
    }
}
</style>