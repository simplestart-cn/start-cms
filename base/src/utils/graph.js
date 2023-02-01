/**
 * 此方法是将节点根据关系表重新进行排序
 * @params [node] Array
 * @params [edge] Array node关系表 [{source: {cell:'123'}, target: {cell: '321'}}] 表示node中存在id为'123'的节点连接着id为'321'的节点
 * @returns Array
 * 将关系表先进行头尾排序，后直接将node中的值根据这个关系表进行筛选，即节点重新排序。
 * */
export function sortNode(node, edge) {
  const newEdge = sortEdge(edge)
  const sortNode = newEdge.length ? [] : node
  newEdge.forEach(item => {
    for (let i = 0; i < node.length; i++) {
      if (node[i].id === item) {
        sortNode.push(node[i])
        break
      }
    }
  })
  return sortNode
}
/**
 * 此方法是将关系表重新进行排序
 * @params [edge] Array node关系表 [{source: {cell:'123'}, target: {cell: '321'}}] 表示node中存在id为'123'的节点连接着id为'321'的节点
 * @returns Array
 * 关系表排序，随机选取一个值，将头尾进行标记，
 * 比较下一个尾部与头部的cell是否相同（相同即插入头部）
 * 或下一个头部是否与原数组的尾部相同（同理上方）
 * 没有则下一个。
 * */
function sortEdge(edge) {
  if (edge.length) {
    // 重新返回新的关系表。
    const newEdge = edge.map(item => {
      return {
        source: item.source.cell,
        target: item.target.cell
      }
    })
    const sortEdge = [newEdge[0]]
    let left = sortEdge[0].source
    let right = sortEdge[0].target
    newEdge.splice(0, 1)
    for (let i = 0; sortEdge.length < edge.length; i++) {
      let j = 0
      while (j < newEdge.length) {
        if (newEdge[j].source === right) {
          sortEdge.push(newEdge[j])
          right = newEdge[j].target
          newEdge.splice(j, 1)
        } else if (newEdge[j].target === left) {
          sortEdge.unshift(newEdge[j])
          left = newEdge[j].source
          newEdge.splice(j, 1)
        } else {
          j++
        }
      }
    }
    const Edge = []
    sortEdge.forEach((item, index) => {
      if (index === 0) {
        Edge.push(item.source)
        Edge.push(item.target)
        return
      }
      Edge.push(item.target)
    })
    return Edge
  }
  return []
}
