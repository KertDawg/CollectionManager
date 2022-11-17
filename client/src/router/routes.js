
const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      { name: "welcome", path: "welcome", component: () => import("pages/Index.vue") },
      { name: "home", path: "home", component: () => import("pages/UserHome.vue") },
      { name: "settings", path: "settings", component: () => import("pages/Settings.vue") },
      { name: "tags", path: "tags", component: () => import("pages/Tags.vue") },
      { name: "locations", path: "locations", component: () => import("pages/Locations.vue") },
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
