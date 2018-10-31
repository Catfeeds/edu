<template>
  <div class="main">
    <p class="company">{{ copyright }}</p>
    <p class="option">{{ suggest }}</p>
  </div>
</template>

<script>
export default {
  data () {
    return {
      copyright: 'Powered by Chuandge Teaching System V3.0 2017 Chuandge 版权',
      suggest: '意见建议：cloud_computing@guoteng.com.cn'
    }
  }
}
</script>

<style scoped>
.main{
  height: 40px;
  background: #72717f;
  color: #000;
  text-align: center;
  font-size: 1.4rem;
  padding: 30px 0;
  width: 100%;
  margin-top: 100px;
}
.main p {
  line-height: 30px;
}
</style>
