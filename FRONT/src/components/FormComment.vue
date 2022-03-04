<template>
  <div>
    <div class="mb-5" style="height: 50px">
      <a
        :href="'/comments/' + post_id"
        class="btn btn-primary"
        style="float: right"
        id="back"
        >Retour vers le post</a
      >
    </div>
    <div>
      <form
        id="form"
        @submit.prevent="editmode ? updateComment() : addComment()"
      >
        <div class="modal-body">
          <div class="form-group">
            <label>Commentaire</label>
            <input
              v-model="form.content"
              type="text"
              name="name"
              class="form-control"
              id="comment"
            />
          </div>
        </div>
        <div class="modal-footer">
          <button
            v-show="editmode"
            type="submit"
            class="btn btn-success"
            id="update"
          >
            Mettre à jour
          </button>
          <button
            v-show="!editmode"
            type="submit"
            class="btn btn-success"
            id="create"
          >
            Créer
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      editmode:
        window.location.pathname.split("/")[2] != "" &&
        window.location.pathname.split("/")[2] != undefined
          ? true
          : false,
      comment: [],
      comment_id: window.location.pathname.split("/")[2],
      post_id: window.location.search.slice(-1),
      form: {
        post_id: "",
        content: "",
      },
    };
  },
  methods: {
    loadComment() {
      if (this.comment_id != "" && this.comment_id != undefined) {
        this.axios
          .get("https://127.0.0.1:8000/api/comment/" + this.comment_id)
          .then((res) => {
            this.comment = res.data;
            this.form.post_id = this.comment.post_id;
            this.form.content = this.comment.content;
          });
      } else {
        this.form.post_id = this.post_id;
      }
    },
    addComment() {
      this.axios
        .post("https://127.0.0.1:8000/api/comment/" + this.post_id, this.form)
        .then((res) => {
          window.location.href = "/comments/" + this.post_id;
        });
    },
    updateComment() {
      this.axios
        .put("https://127.0.0.1:8000/api/comment/" + this.comment_id, this.form)
        .then((res) => {
          window.location.href = "/comments/" + this.post_id;
        });
    },
  },
  created() {
    this.loadComment();
  },
};
</script>