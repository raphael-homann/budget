<template>
  <h1>edit movements (budget : {{ budget?.name }} {{ budget?.id }})</h1>
  <v-container>

    <v-dialog :model-value="movementModal" max-width="500">
      <bg-movement-edit-form :entity-manager="entityManager" :movement="movementModal" @close="movementModal=null"
                             @save="movementModal=null"></bg-movement-edit-form>
    </v-dialog>

    <v-btn @click="create" color="primary">Ajouter</v-btn>

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
import EntityChangedEvent from "../../../custom/npm-src/SynergyTS-npm/Data/Event/EntityChangedEvent";
import ListItemChangedEvent from "@efrogg/synergy/Data/Event/ItemListChangedEvent";

const entityManager: EntityManager = store.entityManager;
const movementRepository = entityManager.getRepository(Movement);
const budgetRepository = entityManager.getRepository(Budget);

const MovementList = defineComponent({
  components: {BgMovementEditForm},
  data(): {
    movements: Movement[],
    movementModal: null | Movement,
    entityManager: EntityManager,
    budgetId: null | number,
    budget: null | Budget,
    headers: { title: string, value: string, sortable?: boolean }[]
  } {
    let entityManager: EntityManager = store.entityManager;
    return {
      movements: [],
      movementModal: null,
      entityManager: entityManager,
      budgetId: null,
      budget: null,
      headers: [
        {title: 'Amount', value: 'amount'},
        {title: 'Commentaire', value: 'comment'},
        {title: 'Catégorie', value: 'category.name', sortable: false},
        {title: 'Enveloppe', value: 'category.envelope.name', sortable: false},
        {title: 'Actions', value: 'actions', sortable: false},
      ]
    }
  }, computed: {},
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
