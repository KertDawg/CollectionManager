import mitt from "mitt";
import { VueMasonryPlugin } from "vue-masonry/src/masonry.plugin.js";
//import NotesList from "../components/NotesList";


export default async ({ app }) => {
  const emitter = mitt();
  app.config.globalProperties.emitter = emitter;
  app.use(VueMasonryPlugin);
  //app.component("NotesList", NotesList);
};
