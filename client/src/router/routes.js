
const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      { name: "home", path: "home", component: () => import("pages/Index.vue") },
      { name: "item", path: "item/:ItemID", component: () => import("pages/Item.vue"), props: true },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    component: () => import("pages/Error404.vue")
  }
]

export default routes
