<template>
  <h1>edit movements (budget : {{ budget?.name }} {{ budget?.id }})</h1>
  <v-container>

    <v-dialog v-model="categoryModal" width="auto" :close-on-back="false">
      <bg-category-edit-form
          :category="categoryModal"
          :entity-manager="entityManager"
          @close="categoryModal=null"
          @save="onCategorySaved"
      />
    </v-dialog>

    <v-dialog :model-value="movementModal" max-width="500">
      <bg-movement-edit-form :entity-manager="entityManager" :movement="movementModal" @close="movementModal=null"
                             @save="movementModal=null"></bg-movement-edit-form>
    </v-dialog>


    <v-dialog v-model="showImportModal" max-width="500" @close="showImportModal=false">
      <import-modal :budget-id="budgetId" @finished="showImportModal=false"></import-modal>
    </v-dialog>


    <v-btn @click="create" color="primary">Ajouter</v-btn>
    <v-btn @click="showImportModal=true">Importer</v-btn>

    <v-data-table
        :headers="headers"
        :items="movements"
        :items-per-page="25"
        class="elevation-1"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon @click="movementModal=item.clone()">mdi-pencil</v-icon>
        <v-icon @click="deleteMovement(item)">mdi-delete</v-icon>
      </template>

      <template v-slot:item.amount="{ item }">
        <v-icon v-if="item.amount>0" color="green">mdi-plus</v-icon>
        <v-icon v-else color="red">mdi-minus</v-icon>
            {{item.amount}}
      </template>

      <template v-slot:item.category="{ item }">
        {{ item.category?.name }}
        <v-autocomplete
            :items="categories"
            item-title="name"
            item-value="id"
            v-model="item.categoryId"
            label="Catégorie"
            required
            @update:modelValue="entityManager.save(item)"
            @update:search="categorySearchUpdated"
        >
          <template v-slot:no-data>
            <v-btn @click="newCategory(item)">create !</v-btn>
          </template>
        </v-autocomplete>
<!--        <v-icon @click="movementModal=item.clone()">mdi-pencil</v-icon>-->
<!--        <v-icon @click="deleteMovement(item)">mdi-delete</v-icon>-->
      </template>
    </v-data-table>
  </v-container>

</template>

<script lang="ts">
import {defineComponent} from "vue";
import {store} from "../../service/store";
import Movement from "../../Data/Entity/Movement";
import BgMovementEditForm from "../../Data/Form/BgMovementEditForm.vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
import Budget from "../../Data/Entity/Budget";
import Criteria from "@efrogg/synergy/Data/Criteria/Criteria";
import EqualsFilter from "@efrogg/synergy/Data/Criteria/Filter/EqualsFilter";
import FieldSort from "@efrogg/synergy/Data/Criteria/Sort/FieldSort";
import ListItemChangedEvent from "@efrogg/synergy/Data/Event/ItemListChangedEvent";
import Category from "../../Data/Entity/Category";
import BgCategoryEditForm from "../../Data/Form/BgCategoryEditForm.vue";
import ImportModal from "../../component/import-modal.vue";

const entityManager: EntityManager = store.entityManager;
const movementRepository = entityManager.getRepository(Movement);
const budgetRepository = entityManager.getRepository(Budget);

const MovementList = defineComponent({
  components: {ImportModal, BgCategoryEditForm, BgMovementEditForm},
  data(): {
    currentCategorySearch: string,
    currentMovement: Movement|null,

    movements: Movement[],
    movementModal: null | Movement,
    categoryModal: null | Category
    entityManager: EntityManager,
    budgetId: null | number,
    budget: null | Budget,
    headers: { title: string, value: string, sortable?: boolean }[],
    showImportModal: boolean
  } {
    let entityManager: EntityManager = store.entityManager;
    return {
      currentCategorySearch: '',
      currentMovement: null,
      movements: [],
      movementModal: null,
      categoryModal: null,
      entityManager: entityManager,
      budgetId: null,
      budget: null,
      headers: [
        {title: 'Amount', value: 'amount'},
        {title: 'Commentaire', value: 'comment'},
        {title: 'Catégorie', value: 'category', sortable: false},
        {title: 'Enveloppe', value: 'category.envelope.name', sortable: false},
        {title: 'Actions', value: 'actions', sortable: false},
      ],
      showImportModal: false
    }
  }, computed: {
    categories() {
      return store.entityManager.getRepository(Category).getItems();
    }
  },
  methods: {
    initMovements() {
      console.log('initMovements', this.budgetId)
      if (this.budgetId) {
        let criteria = new Criteria();
        criteria
            .addFilter(new EqualsFilter('budget.id', this.budgetId))
            .addSort(new FieldSort('name', 'asc'))
        // .addSort(new CustomSort((a: Movement, b:Movement) => {
        //   return a.name.localeCompare(b.name)
        // }))
        ;
        // criteria.addFilter(new CustomFilter('budget.id', (id: any) => id === this.budgetId));                          // works
        this.movements = movementRepository.search(criteria).getItems();                // works too
        // this.movements = movementRepository.findItemsBy({'budget.id': new EqualsFilter('budget.id',this.budgetId)}).getItems(); // works
        // this.movements = movementRepository.findItemsBy({'budget.id': this.budgetId}).getItems(); // works again
      }
    },
    categorySearchUpdated(search: string) {
      // save search for when we create a new category
      this.currentCategorySearch = search;
    },
    newCategory(movement: Movement) {
      // open the new category modal ??
      this.currentMovement = movement;

      let category = new Category();
      category.name = this.currentCategorySearch;
      category.budgetId = this.budgetId;
      // with modal
      this.categoryModal = category;
    },
    onCategorySaved(category: Category) {
      this.categoryModal = null;
      console.log('category saved', category)
      if(null !== this.currentMovement) {
        this.currentMovement.categoryId = category.getId();
        this.entityManager.save(this.currentMovement);
      }
    },
    initBudget() {
      if (this.budgetId) {
        this.budget = budgetRepository.get(this.budgetId);
        console.log('this.budget', this.budget)
      }
    },
    deleteMovement(movement: Movement) {
      this.entityManager.delete(movement);
      // this.initMovements();
    },
    edit(movement: Movement) {
      this.movementModal = movement.clone();
      // this.$router.push({name: 'movement-edit', params: {id: movement.getId()}});
    },
    create() {
      let movement = new Movement();
      movement.budgetId = this.budgetId;
      this.movementModal = movement;
    },
    unbindRepository() {
      budgetRepository.removeEventListener(ListChangedEvent.TYPE, this.initBudget);
      movementRepository.removeEventListener(ListChangedEvent.TYPE, this.initMovements);
      movementRepository.removeEventListener(ListItemChangedEvent.TYPE, this.initMovements);
    },
    bindRepository() {
      budgetRepository.addEventListener(ListChangedEvent.TYPE, this.initBudget);
      movementRepository.addEventListener(ListChangedEvent.TYPE, this.initMovements);
      movementRepository.addEventListener(ListItemChangedEvent.TYPE, this.initMovements);   // triggers sorting and filtering
    },
  },
  unmounted() {
    this.unbindRepository();
    this.bindRepository();
  },
  mounted() {
    this.budgetId = parseInt(this.$route.params.budgetId);
    this.bindRepository()
    this.initMovements();
  },
});
export default MovementList;
</script>

<style scoped>

</style>
