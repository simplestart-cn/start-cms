<template>
    <el-scrollbar ref="scrollContainer" :vertical="false" class="scroll-container">
        <el-menu :default-active="activeMenu" :background-color="backgroundColor" :text-color="textColor" :unique-opened="false" :active-text-color="textColor" :collapse-transition="false" mode="horizontal">
            <menu-item v-for="route in appNavbar" :key="route.path" :item="route" :collapse="isCollapse" :base-path="route.path" />
        </el-menu>
    </el-scrollbar>
</template>

<script>
import { mapGetters } from "vuex";
import MenuItem from "./MenuItem";
import variables from "@/styles/variables.scss";

export default {
    components: { MenuItem },
    computed: {
        ...mapGetters(["config","appNavbar", "sidebar"]),
        textColor() {
            return this.config.text_color;
        },
        backgroundColor() {
            const theme = this.config.background_color;
            // 修改全局scss在styles/variables.scss中定义，hover和active样式定义在sidebar.scss中
            const subMenuHover = this.mixColor(theme, 0.2) || "";
            document.getElementsByTagName("body")[0].style.setProperty("--myStyle", subMenuHover);
            return theme;
        },
        activeMenu() {
            const route = this.$route;
            const { meta, path } = route;
            // if set path, the sidebar will highlight the path you set
            if (meta.activeMenu) {
                return meta.activeMenu;
            }
            return path;
        },
        variables() {
            return variables;
        },
        isCollapse() {
            return !this.sidebar.opened;
        },
    },
    methods: {
        getColorChannels(color) {
            color = color.replace("#", "");
            if (/^[0-9a-fA-F]{3}$/.test(color)) {
                color = color.split("");
                for (let i = 2; i >= 0; i--) {
                    color.splice(i, 0, color[i]);
                }
                color = color.join("");
            }
            if (/^[0-9a-fA-F]{6}$/.test(color)) {
                return {
                    red: parseInt(color.slice(0, 2), 16),
                    green: parseInt(color.slice(2, 4), 16),
                    blue: parseInt(color.slice(4, 6), 16),
                };
            } else {
                return {
                    red: 255,
                    green: 255,
                    blue: 255,
                };
            }
        },
        mixColor(color, percent) {
            let { red, green, blue } = this.getColorChannels(color);
            if (percent > 0) {
                // shade given color
                red *= 1 - percent;
                green *= 1 - percent;
                blue *= 1 - percent;
            } else {
                // tint given color
                red += (255 - red) * percent;
                green += (255 - green) * percent;
                blue += (255 - blue) * percent;
            }
            return `rgb(${Math.round(red)}, ${Math.round(green)}, ${Math.round(blue)})`;
        }
    },
};
</script>
<style lang="scss" scoped>
.el-menu.el-menu--horizontal {
    border-bottom: none;
    display: flex;
}
.el-menu--horizontal ::v-deep .el-menu--popup {
    padding: 0;
}
</style>
