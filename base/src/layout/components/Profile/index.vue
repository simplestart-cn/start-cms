<template>
    <div class="profile-container">
        <el-dropdown class="profile-item hover-effect" trigger="click" :style="'color:'+textColor+';'">
            <div class="avatar-wrapper">
                <img v-if="user.avatar" :src="user.avatar+'?imageView2/1/w/80/h/80'" class="user-avatar">
                <span>{{ user.name }}</span>
                <i class="el-icon-caret-bottom" />
            </div>
            <el-dropdown-menu slot="dropdown">
                <router-link to="/core/user/current">
                    <el-dropdown-item>个人设置</el-dropdown-item>
                </router-link>
                <el-dropdown-item divided>
                    <span style="display:block;" @click="handleLogout">退出登录</span>
                </el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex';
export default {
    props: {
        textColor: {
            type: String,
            default: '#323232'
        }
    },
    computed: {
        ...mapGetters([
            'user'
        ]),
    },
    methods: {
        ...mapActions({
            logout: "user/logout"
        }),
        async handleLogout() {
            await this.logout();
            this.$router.push(`/?redirect=${this.$route.fullPath}`);
        }
    }
}
</script>
<style lang="scss" scoped>
.profile-container {
        flex-shrink: 0;
        float: right;
        height: 100%;
        padding-right: 15px;

        &:focus {
            outline: none;
        }

        .profile-item {
            display: inline-flex;
            padding: 0 8px;
            margin-right: 10px;
            height: 100%;
            font-size: 16px;
            vertical-align: text-bottom;

            &.hover-effect {
                cursor: pointer;
                transition: background 0.3s;

                &:hover {
                    background: rgba(0, 0, 0, 0.025);
                }
            }
        }

        .avatar-wrapper {
            position: relative;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-shirnk: 0;
            .user-avatar {
                margin-right: 5px;
                cursor: pointer;
                width: 32px;
                height: 32px;
                border-radius: 16px;
            }

            .el-icon-caret-bottom {
                cursor: pointer;
                position: absolute;
                right: -20px;
                font-size: 12px;
            }
        }
    }
</style>
