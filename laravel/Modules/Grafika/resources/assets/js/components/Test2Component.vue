<template>
  <div class="container">
    <div class="table">
      <thead>
        <tr>
          <td>намменвание</td>
          <td>ссылка</td>
        </tr>
      </thead>
      <tbody>
        <tr v-for="value in url">
          <td>{{value.title}}</td>
          <td>{{value.url}}</td>
        </tr>
      </tbody>
    </div>
  </div>
</template>

<script>
export default {
  props: ["url"],
  mounted() {
    this.update();
    console.log("Component mounted.");
  },
  methods: {
    update: function() {
      console.log(this.url);
    }
  }
};
</script>
