/**
 * xiegaolei
 */
import { objectMerge } from '@/utils'

// 格式化成多维数组
function listToTree(list, root = 0, pk = 'id', pid = 'pid', child = 'children', other = null) {
  const tree = []
  if (list) {
    const newList = JSON.parse(JSON.stringify(list))
    newList.forEach(item => {
      if (item[pid] === root) {
        if (other !== null) {
          item = objectMerge(item, other)
        }
        const children = listToTree(list, item[pk], pk, pid, child, other)
        if (children.length) {
          item[child] = children
        }
        tree.push(item)
      }
    })
  }
  return tree
}

// 格式化成一维数组
function treeToList(tree, child = 'children') {
  let list = []
  if (tree) {
    const newTree = JSON.parse(JSON.stringify(tree))
    newTree.forEach(item => {
      list = list.concat(item)
      if (item[child]) {
        list = list.concat(treeToList(item[child]))
      }
    })
  }
  list.forEach(item => {
    item.children = []
  })
  return list
}

// 获取所有父id
function getParentsId(list, id, pk = 'id', pid = 'pid') {
  let ids = []
  if (list) {
    list.forEach(item => {
      if (item[pk] === id) {
        if (item[pid] != 0) ids.unshift(item[pid])
        const parent = getParentsId(list, item[pid], pk, pid)
        if (parent.length) {
          ids = parent.concat(ids)
        }
      }
    })
  }
  return ids
}

// 获取所有子id
function getChildrenId(list, id, pk = 'id', pid = 'pid') {
  let tree = []
  if (list) {
    list.forEach(item => {
      if (item[pk] === id) {
        tree.unshift(item[pid])
        const parent = getChildrenId(list, item[pid], pk, pid)
        if (parent.length) {
          tree = tree.concat(parent)
        }
      }
    })
  }
  return tree
}

// 更新列表值
function updateList(list, key, val, source) {
  if (list) {
    list.map(item => {
      if (item[key] === val) {
        return objectMerge(item, source)
      } else {
        updateList(item.children, key, val, source)
      }
    })
  }
  return list
}

// 递归添加值
function createTree(tree, key, val) {
  let newTree = []
  if (tree) {
    newTree = tree.map(item => {
      item[key] = val
      if (item.children) {
        createTree(item.children, key, val)
      }
      return item
    })
  }
  return newTree
}

// 递归修改值
function updateTree(tree, key, val, source) {
  let newTree = []
  if (tree) {
    newTree = tree.map(item => {
      if (item[key] === val) {
        objectMerge(item, source)
        if (item.children) {
          delete source[key]
          item.children = updateTree(item.children, 'pid', item.id, source)
        }
      } else if (item.children) {
        item.children = updateTree(item.children, key, val, source)
      }
      return item
    })
  }
  return newTree
}

// 通过子节点的数据，计算出父节点的数据
function childrenToParent(tree, key) {
  if (tree) {
    let sum = 0
    let avg = 0
    tree.forEach(item => {
      if (item.children) {
        avg += childrenToParent(item.children, key)
        item[key] = avg
      }
      sum += item[key]
    })
    avg = Math.floor(sum / tree.length)
    return avg
  }
}

function getChildren(tree, key, value)
{
  if(tree) {
    if(value instanceof Array){
      for(let i = 0 ; i < value.length ; i++){
        let child = tree.find(item => item[key] == value[i])
        if(child && child.children){
          return getChildren(child.children, key, value[i+1])
        }
        return  child == 'undefined' ? false : child
      }
    }else{
      let child = tree.find(item => item[key] == value)
      if(child && child.children){
        return getChildren(child.children, key, value)
      }
      return  child == 'undefined' ? false : child
    }
  }
}

const tree = {
  listToTree,
  treeToList,
  getChildren,
  getParentsId,
  getChildrenId,
  updateTree,
  updateList,
  createTree,
  childrenToParent,
}

export default tree

