<template>
  <div class="content">
    <div class="inner-content">
    <div class="close-btn" @click="close">
      <img :src="'images/close.png'" width="25" height="25">
    </div>
    <tree-view :data="jsonsource" :options="{ maxDepth: 3 }"></tree-view>
    </div>
  </div>
</template>

<script>
export default {
  props: ['jsonsource'],
  data() {
    return {
      tree_options: {
        maxDepth: 4,
        rootObjectKey: "details",
        modifiable: false,
        link: false,
        limitRenderDepth: 5
      }
    };
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>

<style>
  .content{
    position: absolute;
    z-index: 5;
    top: 0;
    padding: 1.5rem 2rem;
    margin: 2rem;
    min-height: 100vh;
    max-width: 100%;
    background-color: #FFF;
  }
  .inner-content{
    width: 96%;
    margin: auto;
  }
  .close-btn{
    text-align: right;
    cursor: pointer;
  }
  .tree-view-item{
    font-family: "Nunito", sans-serif !important;
  }
  .tree-view-item-leaf{
    white-space: normal !important;
  }
  .tree-view-item{
    font-size: 12px !important;
  }
  .tree-view-item-key{
    font-size: 14px;
    font-weight: bold !important;
  }
</style>