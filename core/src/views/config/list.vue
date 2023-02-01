<template>
  <div class="app-container">
    <el-row class="handle-container" align="bottom">
      <el-tabs class="margin-top-sm" v-model="appIndex" type="card" :before-leave="handleApp">
        <el-tab-pane
          v-for="(item, key) in apps"
          :key="key"
          :label="item.title"
          :name="key">
          <form-create v-if="key === appIndex" :rule="item.fields" :option="formOption" @mounted="formMounted(appIndex, key, $event)"></form-create>
          <div class="flex" v-if="key === appIndex">
            <group-page v-model="item.groups" :tree-enabled="false" :edit-enabled="false" @selected="handleGroup"></group-page>
            <div class="flex-auto" v-if="item.groups.length > 0">
              <form-create :rule="item.groups[groupIndex].fields" :option="formOption" @mounted="formMounted(groupIndex, 'group', $event)"></form-create>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </el-row>
    <el-row type="flex" justify="center">
      <el-button v-waves type="success" icon="el-icon-upload" :loading="formLoading" @click="handleSubmit">提交保存</el-button>
    </el-row>
  </div>
</template>

<script>
import { getList, updateList, remove } from '@/api/config'
import groupPage from './group'
import { mapActions } from 'vuex'
export default {
  components: { groupPage },
  data() {
    return {
      // 表单配置
      formOption: {
        form: {
          showMessage: true,
          inlineMessage: false,
          statusIcon: true,
          validateOnRuleChange: true,
          labelWidth: '150px',
          size: 'medium'
        },
        row: {
          gutter: 10
        },
        submitBtn: false,
        resetBtn: false
      },
      formLoading: false,
      apps: [],
      appIndex: '',
      appForm: {},
      groupIndex: 0,
    }
  },
  mounted() {
    this.fetchConfig()
  },
  methods: {
    async fetchConfig() {
      let apps = {};
      let { data: list } = await getList();
      list.forEach((item, index) => {
        // 应用分组
        if(!apps[item.app]){
          apps[item.app] = {
            name: item.app,
            title: item.app_title,
            groups: [],
            fields: [],
          }
          index == 0 ? this.appIndex = item.app : ''
        }
        // 配置格式化
        item.props = item.props || {}
        if(item.remark){
          item.suffix = { type: 'div', class: 'el-form-item__remark', children: [item.remark] }
        }
        if(item.type == 'file-library' && typeof item.value != 'string'){
          item.value += '';
        }
        // 配置分组
        if(item.group){
          let group = apps[item.app].groups.find(group => group.title == item.group)
          if(typeof group == 'undefined'){
            apps[item.app].groups.push({
                title: item.group,
                fields: [item]
              })
          }else{
            group.fields.push(item)
          }
        }else{
          apps[item.app].fields.push(item)
        }
      });
      this.apps = apps
    },
    handleApp() {
      this.groupIndex = 0
    },
    // 切换
    handleGroup(title) {
      this.groupIndex = this.apps[this.appIndex].groups.findIndex(item => item.title === title)
    },
    handleSubmit() {
      let isEmpty = (obj) => (JSON.stringify(obj) === '{}') ? true : false;
      let fields = []
      for (let a in this.appForm) {
        let rule = JSON.parse(JSON.stringify(this.appForm[a].model()))
        if (!isEmpty(rule)) {
          this.appForm[a].validate((valid, fail) => {
              if(valid){
                  //todo 表单验证通过
                  for (let k in rule) {
                    fields.push({
                      id: rule[k].id,
                      type: rule[k].type,
                      title: rule[k].title,
                      field: rule[k].field,
                      value: rule[k].value,
                      props: rule[k].props,
                      default: rule[k].default,
                      options: rule[k].options
                    })
                  }
              }else{
                  //todo 表单验证未通过
                  return false
              }
          })
        }
      }
      this.formLoading = true
      updateList({ list: fields }).then(response => {
        if (response.code === 0) {
          this.$message.success(response.msg)
        } else {
          this.$message.error(response.msg)
        }
        this.formLoading = false
        setTimeout(()=>{
          window.location.reload();
        },500)
      }).catch(error => {
        this.$message.error(error.message)
        this.formLoading = false
      })
    },
    formMounted(index, name, $f) {
      this.appForm[name] = $f
    },
  }
}
</script>
<style type="text/scss" lang="scss" scoped>
  .app-container {
    padding-bottom: 100px;
  }
</style>
