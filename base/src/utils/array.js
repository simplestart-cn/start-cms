/**
 * 数组去重
 * @param { Array } data
 * @param { String } key 用来去重的标记，默认id
 * @return { Array } list
 * */
function duplicateRemoval(data, key = 'id') {
  const list = data.filter((item, index, arr) => {
    let currentIndex = -1
    arr.some((arrItem, arrIndex) => {
      currentIndex = arrIndex
      return arrItem[key] === item[key]
    })
    return currentIndex === index
  })
  return list
}

/**
 * 修改数据某一处数据
 * @param { Array } initList 数组列表
 * @param { Array } alterList 需要修改的数组列表
 * @param { String } key 匹配数据是否同一条数据 默认id
 * @param { String } alterKey  需要修改的值的键名
 * @param initDefault 当没有匹配时给值的默认值，默认为''
 * @return { Array } result 返回数据
 * */
function alterArrayOfKey(initList, alterList, key, alterKey, initDefault = '') {
  const result = JSON.parse(JSON.stringify(alterList))
  result.forEach(item => {
    item[alterKey] = initDefault
    initList.some(initItem => {
      return (item[key] === initItem[key]) && (item[alterKey] = initItem[alterKey])
    })
  })
  return result
}

/**
 * @param data = { Array } 原数组
 * @param childrenName = { String } 孩子键名
 * @param childrenList = { Array } 孩子内部对象字段名数组
 * @param identify = { String } 判断唯一
 * @constructor
 */
 function ArrayOfChildren(data, childrenName, childrenList = [], ...identify) {
  const list = []
  data.forEach(item => {
    const cObj = {}
    childrenList.forEach(cItem => {
      cObj[cItem] = item[cItem]
      delete item[cItem]
    })
    const index = list.findIndex(lItem => {
      return identify.every(key => lItem[key] === item[key])
    })
    if (index > -1) {
      if (!list[index][childrenName]) {
        list[index][childrenName] = []
      }
      list[index][childrenName].push(cObj)
    } else {
      if (!item[childrenName]) {
        item[childrenName] = []
      }
      item[childrenName].push(cObj)
      list.push(item)
    }
  })
  return list
}

export default {
  duplicateRemoval,
  alterArrayOfKey,
  ArrayOfChildren
}
