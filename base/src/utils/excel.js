import XLSX, { utils } from 'xlsx'
import { getArrByKey } from './index'

/**
 * @description 将表格规则转化为二维数组
 * @param { Array } rule 规则规格列表
 * @param { Array } list 数组数据
 * @param { String } label 在规则中对应的表头字段，默认为'label'
 * @param { String } value 在规则中对应的数据键名，默认为'value'
 * @return { Object } data 返回的表格数据
 * @return { Array } data.title 表头，即表格首行展示的内容，如：['姓名','年龄','性别','地址']
 * @return { Array } data.body  表格内容，二维数组，每一行的内容为一个数组，与表头对应，如:[['王小虎',28,'男','aa'],['王大虎',29,'男','aa']]
 * */
function ruleToArray(rule, list = [], label = 'label', value = 'value') {
  const title = []
  const body = []
  list.forEach(item => {
    const content = []
    rule.forEach(key => {
      content.push(item[key[value]])
    })
    body.push(content)
  })
  const data = {}
  data.title = getArrByKey(rule, label)
  data.body = body
  return data
}

/**
 * @description             依据数据导出表格，一般是根据查询出的数据导出表格
 * @param {Object} option   Object--配置对象
 * @param option.title      Array--表头，即表格首行展示的内容，如：['姓名','年龄','性别','地址']
 * @param option.body       Array--表格内容，二维数组，每一行的内容为一个数组，与表头对应，如:[['王小虎',28,'男','aa'],['王大虎',29,'男','aa']]
 * @param option.name       String--文件名，如:'demo'
 * @param option.suffix     String--文件后缀名，如'xlsx'
 * @param option.merges     Array--表格的单元格合并信息，如[
 { s: { r: 0, c: 0 }, e: { r: 1, c: 0 } },-----表示A1和A2单元格合并
 { s: { r: 0, c: 1 }, e: { r: 0, c: 2 } },-----表示B1和C1单元格合并
 { s: { r: 0, c: 3 }, e: { r: 0, c: 4 } },-----表示D1和E1单元格合并
 { s: { r: 0, c: 5 }, e: { r: 1, c: 5 } }-----表示F1和F2单元格合并
 ]------s:start,e:end,r:row,c:cell;
 */
function exportFromArray(option) {
  const config = Object.assign({}, { name: 'demo', suffix: 'xlsx' }, option)
  const { title, body, name, suffix, merges } = config
  const array = [title].concat(body)
  const workBook = utils.book_new() // 创建workBook
  const workSheet = utils.aoa_to_sheet(array) // 将数组转换成workSheet
  const fileName = name + '.' + suffix
  workSheet['!merges'] = merges
  utils.book_append_sheet(workBook, workSheet, name)
  XLSX.writeFile(workBook, fileName, { bookType: suffix, type: 'buffer' })
}

/**
 * @description 将导入的数据键值进行修改为对应的字段。
 * @param { Object } option 配置信息
 * @param { Array } option.title 导入数据的表头
 * @param { Array } option.body 导入数据的内容
 * @param { Array } rule 规则数组
 * @param { String } label 在规则中对应的表头字段，默认为'label'
 * @param { String } value 在规则中对应的数据键名，默认为'value'
 * @return { Array } data 修改完成后的数组
 * */
function arrayByRule(option, rule, label = 'label', value = 'value') {
  const data = []
  option.body.forEach(item => {
    const body = {}
    rule.forEach(key => {
      body[key[value]] = item[key[label]]
    })
    data.push(body)
  })
  return data
}

/**
 * @description  解析表格文件数据，返回表格中内容，目前暂不支持导入有单元格合并的表格
 * @param    {Object} file    导入的文件，二进制数据流
 * @returns  {Object} data    返回的表格数据
 * @returns  {Array}  data.title   表头
 * @returns  {Array}  data.body    表格数据
 */
async function importFromLocal(file) {
  const workBook = await readerWorkBookFromLocal(file)
  const workSheet = workBook.Sheets[workBook.SheetNames[0]]
  const content = utils.sheet_to_json(workSheet)
  const data = {}
  if(content.length > 0) {
    data.title = Object.keys(content[0])
    data.body = content
  }
  return data
}
/**
 * @description              本地读取文件的方法
 * @param {Object} file      文件流
 */
function readerWorkBookFromLocal(file) {
  const reader = new FileReader()
  reader.readAsBinaryString(file)
  return new Promise(function(resolve, reject) {
    reader.onload = function(e) {
      const fileData = e.target.result
      if (reader.readyState === 2) {
        const workBook = XLSX.read(fileData, { type: 'binary' })
        resolve(workBook)
      } else {
        reject('读取文件失败')
      }
    }
  })
}

export default {
  ruleToArray,
  exportFromArray,
  arrayByRule,
  importFromLocal
}
