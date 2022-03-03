<template>
  <div
    class="row"
    style="
      display: flex;
      justify-content: space-between;
      width: 75%;
      margin: auto;
    "
  >
    <div
      class="card col-3"
      style="width: 18rem"
      v-for="post in posts"
      :key="post.id"
    >
      <div class="card-body">
        <h5 class="card-title">Post {{ post.id }}</h5>
        <p class="card-text">{{ post.title }}</p>
        <a :href="'/comments/' + post.id" class="btn btn-primary"
          >Voir les commentaires</a
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      posts: [],
    };
  },
  methods: {
    loadPosts() {
      this.axios
        .get("https://127.0.0.1:8000/api/posts")
        .then((res) => (this.posts = res.data));
    },
  },
  created() {
    this.loadPosts();
  },
};
</script>

<style>
@import "../assets/base.css";

header {
  line-height: 1.5;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

a,
.green {
  text-decoration: none;
  color: hsla(160, 100%, 37%, 1);
  transition: 0.4s;
}
</style>
