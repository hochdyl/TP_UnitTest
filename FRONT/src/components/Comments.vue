<template>
  <div>
    <div class="mb-5">
      <a :href="'/addcomment?post_id=' + post_id" class="btn btn-primary">
        Ajouter un commentaire
      </a>
      <a href="/" class="btn btn-primary" style="float: right"
        >Retour vers l'accueil</a
      >
    </div>
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
        class="card col-3 mb-5"
        style="width: 18rem"
        v-for="comment in comments"
        :key="comment.id"
      >
        <div class="card-body">
          <h5 class="card-title">Commentaire {{ comment.id }}</h5>
          <p class="card-text">{{ comment.content }}</p>
          <a :href="'/addcomment/' + comment.id + '?post_id=' + post_id">
            <i class="fa fa-edit blue"></i>
          </a>
          <a href="#" @click="deleteComment(comment.id)">
            <i class="fa-solid fa-trash-can" style="color: red"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      comments: [],
      post_id: window.location.pathname.split("/")[2],
    };
  },
  methods: {
    loadPosts() {
      this.axios
        .get("https://127.0.0.1:8000/api/post/" + this.post_id)
        .then((res) => (this.comments = res.data.comments));
    },
    deleteComment(comment_id) {
      this.axios
        .delete("https://127.0.0.1:8000/api/comment/" + comment_id)
        .then((res) => {
          this.loadPosts();
        });
    },
  },
  created() {
    this.loadPosts();
  },
};
</script>