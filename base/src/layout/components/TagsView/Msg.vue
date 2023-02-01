<template>
    <el-drawer ref="drawer" :with-header="false" size="500px" :before-close="hidden" :visible.sync="visible" direction="rtl" custom-class="drawer-page" :append-to-body="true" :modal="false">
        <div class="drawer-header flex justify-between align-center">
            <div class="text-df"><i class="el-icon-bell margin-right-xs"></i>最新消息</div>
            <i class="el-icon-close pointer" @click="visible = false"></i>
        </div>
        <div class="drawer-content">
            <el-collapse value="" accordion>
                <el-collapse-item v-for="(item, index) in msg" :key="index">
                    <div class="flex justify-between flex-sub" slot="title">
                        <span class="text-cut flex-auto">{{item.title}}</span>
                        <span class="text-grey flex-shrink padding-lr-sm">{{item.create_time}}2022-03-05</span>
                    </div>
                    <div class="padding" v-html="item.content"></div>
                </el-collapse-item>
            </el-collapse>
        </div>
    </el-drawer>
</template>
<script>
import { mapGetters } from 'vuex'
export default {
    name: 'Msg',
    data() {
        return {
            visible: false,
        }
    },
    computed: {
        ...mapGetters([
            'msg',
        ])
    },
    methods: {
        toggle() {
            this.visible = !this.visible
        },
        hidden() {
            this.visible = false
        },
        show() {
            this.visible = true
        }
    }
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.el-drawer__wrapper {
    top: 40px !important;
}
</style>