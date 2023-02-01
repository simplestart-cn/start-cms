<template>
    <el-row :gutter="20" class="bg-white">
        <div class="padding margin-bottom solid-bottom">
            <span class="margin-left-sm">访问统计</span>
        </div>
        <el-col :span="16">
            <div id="visitLine" style="width:100%;height:400px"></div>
        </el-col>
        <el-col :span="8">
            <div id="visitType" style="width:100%;height:400px"></div>
        </el-col>
    </el-row>
</template>

<script>
import "echarts/theme/macarons.js";
import * as echarts from 'echarts';
export default {
    data() {
        return {};
    },
    methods: {
        drawLine() {
            // 基于准备好的dom，初始化echarts实例
            let myChart = echarts.init(
                document.getElementById("visitLine"),
                "macarons"
            );
            // 绘制图表
            myChart.setOption({
                tooltip: {
                    trigger: "axis",
                },
                legend: {
                    data: ["访问量", "新注册"],
                },
                toolbox: {
                    show: true,
                    feature: {
                        magicType: { show: true, type: ["line", "bar"] },
                        restore: { show: true },
                        saveAsImage: { show: true },
                    },
                },
                calculable: true,
                xAxis: [
                    {
                        type: "category",
                        data: [
                            "0:00",
                            "1:00",
                            "2:00",
                            "3:00",
                            "4:00",
                            "5:00",
                            "6:00",
                            "7:00",
                            "8:00",
                            "9:00",
                            "10:00",
                            "11:00",
                            "12:00",
                            "13:00",
                            "14:00",
                            "15:00",
                            "16:00",
                            "17:00",
                            "18:00",
                            "19:00",
                            "20:00",
                            "21:00",
                            "22:00",
                            "23:00",
                        ],
                    },
                ],
                yAxis: [
                    {
                        type: "value",
                        textStyle: { color: '#fff'}
                    },
                ],
                series: [
                    {
                        name: "访问量",
                        type: "line",
                        data: [
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                            Math.ceil(Math.random() * 1000),
                        ],
                        markLine: {
                            data: [{ type: "average", name: "平均值" }],
                        },
                    },
                    {
                        name: "新注册",
                        type: "line",
                        data: [
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                            Math.ceil(Math.random() * 100),
                        ],
                        markLine: {
                            data: [{ type: "average", name: "平均值" }],
                        },
                    },
                ],
            });
        },
        label_vist() {
            // 基于准备好的dom，初始化echarts实例
            let myChart = echarts.init(
                document.getElementById("visitType"),
                "macarons"
            );
            // 绘制图表
            myChart.setOption({
                title: {
                    text: "用户来源",
                    left: "center",
                },
                tooltip: {
                    trigger: "item",
                    formatter: "{a} <br/>{b} : {c} ({d}%)",
                },
                legend: {
                    orient: "vertical",
                    left: "left",
                    data: ["微信", "抖音", "头条", "百度"],
                },
                series: [
                    {
                        name: "平台",
                        type: "pie",
                        radius: "55%",
                        center: ["50%", "60%"],
                        data: [
                            { value: Math.ceil(Math.random() * 1000), name: "微信" },
                            { value: Math.ceil(Math.random() * 1000), name: "抖音" },
                            { value: Math.ceil(Math.random() * 1000), name: "头条" },
                            { value: Math.ceil(Math.random() * 1000), name: "百度" },
                        ],
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: "rgba(0, 0, 0, 0.5)",
                            },
                        },
                    },
                ],
            });
        },
    },
    mounted() {
        this.drawLine();
        this.label_vist()
    },
};
</script>
<style scoped>
.card {
    min-height: 50vh;
}
</style>