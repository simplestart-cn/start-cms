<template>
    <el-drawer ref="drawer" :with-header="false" size="50%" :before-close="handleClose" :visible.sync="formVisible" direction="rtl" custom-class="drawer-page">
        <div class="drawer-content">
            <form-create v-model="model" :rule="formRule" :option="formOption" @mounted="formMounted" @parent-change="handleChange"></form-create>
            <div class="drawer-footer">
                <el-row type="flex" justify="center">
                    <el-button v-waves @click="handleClose">取 消</el-button>
                    <el-button v-waves :loading="btnLoading" type="primary" @click="handleSubmit">保存</el-button>
                </el-row>
            </div>
        </div>
    </el-drawer>
</template>

<script>
import { getInfo, create, update } from '@/api/region'
import tree from '@/utils/tree'
import { formOption, formRule } from './editForm'
import { mapActions } from 'vuex'

export default {
    name: 'Edit',
    components: {},
    data() {
        return {
            temp: {},
            model: {},
            formRule: formRule,
            formOption: formOption,
            btnLoading: false,
            modelChange: false,
            formVisible: false
        }
    },
    watch: {
        'model.form': {
            handler(val) {
                if (typeof val !== 'undefined') {
                    this.modelChange = true
                }
                /*if (val) {
                  const { region } = this.$store.state.user
                  const parent = val.parent
                  const title = val.title
                  const data = ['中国'].concat(parent.map(item => {
                    const index = region.findIndex(regionItem => regionItem.id === item)
                    return region[index].title
                  }))
                  if (title) {
                    data.push(title)
                  }
                  this.model.setValue('merger_name', data.join(','))
                }*/
            },
            immediate: true,
            deep: true
        },
        formVisible: function () {
            this.handleReset()
        }
    },
    created() {

    },
    methods: {
        handleClose(done) {
            if (this.btnLoading) {
                return
            }
            if (this.modelChange) {
                this.$confirm('更改将不会被保存，确定要取消吗？')
                    .then(_ => {
                        this.formVisible = false
                    })
                    .catch(_ => { })
            } else {
                this.formVisible = false
            }
        },
        handleReset() {
            this.temp = {}
            this.model.form && this.model.resetFields()
            this.modelChange = false
        },
        handleCreate() {
            this.btnLoading = false
            this.formVisible = true
        },
        handleUpdate(id) {
            this.formVisible = true
            getInfo(id).then(response => {
                if (response.code === 0) {
                    this.temp = response.data
                    let parentChecked = tree.getParentsId(this.nodeList, id)
                    if (parentChecked && parentChecked.length > 0) {
                        parentChecked = parentChecked.filter(item => item > 0)
                    } else {
                        parentChecked = [0]
                    }
                    this.model.setValue(this.temp)
                    this.model.setValue('parent', parentChecked)
                }
            })
        },
        handleSubmit() {
            const success = response => {
                if (response.code === 0) {
                    if (!this.temp.id) {
                        this.temp.id = response.data.id
                    }
                    this.$emit('updateRow', this.temp)
                    this.formVisible = false
                    this.$message.success(response.msg)
                } else {
                    this.$message.error(response.msg)
                }
                this.btnLoading = false
            }
            const error = (error) => {
                this.$message.error(error.message)
                this.formLoading = false
                this.btnLoading = false
            }
            this.model.submit(formData => {
                this.btnLoading = true
                delete formData.parent
                Object.assign(this.temp, formData)
                if (!this.temp.id) {
                    create(this.temp).then(success).catch(error)
                } else {
                    update(this.temp).then(success).catch(error)
                }
            })
        },
        handleChange(value) {
            if (value && value.length) {
                this.temp.pid = value[value.length - 1]
            }
        },
        ...mapActions({
            getRegion: 'region/getList'
        }),
        formMounted() {
            const that = this
            that.getRegion({ pid: 0 }).then(response => {
                that.model.mergeRule('parent', {
                    props: {
                        options: [{ id: 0, title: '顶级' }].concat(response),
                        placeholder: '请选择',
                        clearable: true,
                        filterable: true,
                        props: {
                            lazy: true,
                            label: 'title',
                            value: 'id',
                            children: 'children',
                            // form-create上写changeOnSelect用来选择任意一级无效，具体看element_ui决定
                            checkStrictly: true,
                            lazyLoad(node, resolve) {
                                if (node.value) {
                                    that.getRegion({ pid: node.value }).then(response => {
                                        resolve(response)
                                    })
                                }
                                resolve([])
                            }
                        }
                    }
                })
            })
        }
    }
}
</script>
<style type="text/scss" lang="scss" scoped>
</style>
