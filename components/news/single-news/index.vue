<template>
  <div>
    <div v-if="news" id="news-heading" class="container mx-auto bg-white">
      <div class="mx-5 py-2">
        <div class="row block mb-4">
          <h5 class="inline-block">
            <span
              v-if="news.title"
              class="
                inline-block
                px-8
                py-2
                bg-maroon-500
                text-white
                font-bold
                text-xl
                uppercase
              "
              >{{ news.title }}</span
            >
          </h5>
        </div>
        <div class="row mb-4">
          <div class="bg-white flex flex-wrap -mx-5">
            <div class="mb-6 w-full px-5">
              <div class="">
                <div class="image-container mb-3 relative">
                  <div
                    class="
                      w-full
                      h-96
                      py-10
                      overflow-hidden
                      bg-center bg-no-repeat bg-cover
                      relative
                      z-20
                    "
                    :style="{
                      'background-image':
                        'url(' +
                        'http://127.0.0.1:8000/' +
                        news.picture_xl +
                        ')',
                    }"
                  ></div>
                  <span
                    class="
                      absolute
                      top-0
                      bottom-0
                      z-10
                      w-full
                      h-full
                      mx-auto
                      my-auto
                      text-4xl text-maroon-500 text-center
                      flex
                      items-center
                      justify-center
                    "
                    >Loading...</span
                  >
                </div>
                <p v-if="news.created_at" class="text-xs uppercase mb-4">
                  POSTED ON : {{ news.created_at }}
                </p>
                <p v-if="news.description" class="description-container">
                  <span class="text-lg" v-html="news.description"></span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SingleNewsComponent',
  props: {
    newsSlug: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      news: {},
      newsTitle: ''
    }
  },
  head() {
    return {
      title: this.newsTitle
    }
  },
  watch: {
    '$store.state.news'() {
      this.getNews()
    },
    newsSlug() {
      this.getNews()
      this.scrollToHeading()
    },
    $route() {
      this.getNews()
      this.scrollToHeading()
    },
  },
  mounted() {
    if (this.$store.state.news) {
      this.getNews()
      this.scrollToHeading()
    }
  },
  methods: {
    getNews() {
      const newsData = this.$store.state.news.filter(
        (data) => data.slug === this.newsSlug
      )
      this.news = newsData[0]
      if(newsData[0]){
        this.newsTitle = this.news.title
        this.scrollToHeading()
      }
    },
    scrollToHeading(){
      setTimeout(function () {
          document.getElementById('news-heading').scrollIntoView()
      }, 100)
    }
  },
}
</script>

<style>
.done {
  text-decoration: line-through;
}
.description-container p,
.description-container div {
  margin-bottom: 20px;
}
.pdsc-related-modify,
.unitimg,
iframe,
.picture,
.custom-caption,
#pcl-full-content,
.instagram-media {
  display: none;
}
.description-container{
  pointer-events: none;
}
</style>
