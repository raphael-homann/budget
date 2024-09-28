<template>
  <budget-page-header :budget="budget"></budget-page-header>
  <v-breadcrumbs :items="breadcrumbs"></v-breadcrumbs>
  <h1>edit categories (budget : {{ budget?.name }} {{ budget?.id }}</h1>
  <v-container>

    <v-dialog :model-value="categoryModal" max-width="500">
      <bg-category-edit-form :entity-manager="entityManager" :category="categoryModal" @close="categoryModal=null"
                             @save="categoryModal=null"></bg-category-edit-form>
    </v-dialog>

    <v-btn @click="create" color="primary">Ajouter</v-btn>

    <v-data-table
        :headers="headers"
        :items="categories"
        :items-per-page="25"
        class="elevation-1"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon @click="categoryModal=item.clone()">mdi-pencil</v-icon>
        <v-icon @click="deleteCategory(item)">mdi-delete</v-icon>
      </template>
    </v-data-table>
  </v-container>

</template>

<script lang="ts">
import {defineComponent} from "vue";
import {store} from "../../service/store";
import Category from "../../Data/Entity/Category";
import BgCategoryEditForm from "../../Data/Form/BgCategoryEditForm.vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
import Budget from "../../Data/Entity/Budget";
import Criteria from "@efrogg/synergy/Data/Criteria/Criteria";
import EqualsFilter from "@efrogg/synergy/Data/Criteria/Filter/EqualsFilter";
import FieldSort from "@efrogg/synergy/Data/Criteria/Sort/FieldSort";
import ListItemChangedEvent from "@efrogg/synergy/Data/Event/ItemListChangedEvent";
import BudgetPageHeader from "../../component/budget-page-header.vue";

const entityManager: EntityManager = store.entityManager;
const categoryRepository = entityManager.getRepository(Category);
const budgetRepository = entityManager.getRepository(Budget);

const CategoryList = defineComponent({
  components: {BudgetPageHeader, BgCategoryEditForm},
  data(): {
    categories: Category[],
    breadcrumbs: [],
    categoryModal: null | Category,
    entityManager: EntityManager,
    budgetId: null | number,
    budget: null | Budget,
    headers: { title: string, value: string, sortable?: boolean }[]
  } {
    let entityManager: EntityManager = store.entityManager;
    return {
      breadcrumbs: [],
      categories: [],
      categoryModal: null,
      entityManager: entityManager,
      budgetId: null,
      budget: null,
      headers: [
        {title: 'Name', value: 'name'},
        {title: 'Envelope', value: 'envelope.name'},
        {title: 'Actions', value: 'actions', sortable: false}
      ]
    }
  }, computed: {
  },
  methods: {
    initCategories() {
      if (this.budgetId) {
        let criteria = new Criteria();
        criteria
            .addFilter(new EqualsFilter('budget.id', this.budgetId))
            .addSort(new FieldSort('name', 'asc'))
        // .addSort(new CustomSort((a: Category, b:Category) => {
        //   return a.name.localeCompare(b.name)
        // }))
        ;
        // criteria.addFilter(new CustomFilter('budget.id', (id: any) => id === this.budgetId));                          // works
        this.categories = categoryRepository.search(criteria).getItems();                // works too
        // this.categories = categoryRepository.findItemsBy({'budget.id': new EqualsFilter('budget.id',this.budgetId)}).getItems(); // works
        // this.categories = categoryRepository.findItemsBy({'budget.id': this.budgetId}).getItems(); // works again
      }
    },
    updateBreadCrumbs() {
      console.log(this.$router.resolve({name: 'home'}));
      let breadcrumbs = [
        {title: 'Home', disabled: false, href: this.$router.resolve({name: 'home'}).href},
      ];
      if(this.budget)
        breadcrumbs.push({title: this.budget.name, disabled: false, href: this.$router.resolve({name: 'budget-view',params: {id: this.budgetId??1}}).href});

      breadcrumbs.push({title: 'catégories', disabled: true, href: this.$router.resolve({name: 'budget-categories',params: {budgetId: this.budgetId??1}}).href});
      this.breadcrumbs = breadcrumbs;
    },
    initBudget() {
      if (this.budgetId) {
        this.budget = budgetRepository.get(this.budgetId);
        this.updateBreadCrumbs();
        console.log('this.budget', this.budget)
      }
    },
    deleteCategory(category: Category) {
      this.entityManager.delete(category);
      // this.initCategories();
    },
    edit(category: Category) {
      this.categoryModal = category.clone();
      // this.$router.push({name: 'category-edit', params: {id: category.getId()}});
    },
    create() {
      let category = new Category();
      category.budgetId = this.budgetId;
      this.categoryModal = category;
    },
    unbindRepository() {
      budgetRepository.removeEventListener(ListChangedEvent.TYPE, this.initBudget);
      categoryRepository.removeEventListener(ListChangedEvent.TYPE, this.initCategories);
      categoryRepository.removeEventListener(ListItemChangedEvent.TYPE, this.initCategories);
    },
    bindRepository() {
      budgetRepository.addEventListener(ListChangedEvent.TYPE, this.initBudget);
      categoryRepository.addEventListener(ListChangedEvent.TYPE, this.initCategories);
      categoryRepository.addEventListener(ListItemChangedEvent.TYPE, this.initCategories);   // triggers sorting and filtering
    },
  },
  unmounted() {
    this.unbindRepository();
    this.bindRepository();
  },
  mounted() {
    this.budgetId = parseInt(this.$route.params.budgetId);
    this.bindRepository()
    this.initCategories();
    this.initBudget();
  },
});

export default CategoryList;
</script>

<style scoped>

</style>
