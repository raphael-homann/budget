<template>
  <v-navigation-drawer
      location="right"
      :mobile="false"
      permanent
      expand-on-hover
      color="primary"
      width="350"
  >
    <bg-detection-mask-list v-if=budget :budget="budget" :detection-mask-input="detectionMaskModal"
                            :entity-manager="entityManager"></bg-detection-mask-list>
  </v-navigation-drawer>

  <h1>edit movements (budget : {{ budget?.name }} {{ budget?.id }})</h1>

  <v-container>

    <!--    <v-dialog v-model="detectionMaskModal" width="auto" :close-on-back="false">-->
    <!--      <bg-detection-mask-edit-form v-if="detectionMaskModal"-->
    <!--                                   :detection-mask="detectionMaskModal"-->
    <!--                                   :entity-manager="entityManager"-->
    <!--                                   @close="detectionMaskModal=null"-->
    <!--                                   @play="playDetection(detectionMaskModal)"-->
    <!--      />-->

    <!--    </v-dialog>-->

    <!--    <v-dialog v-model="detectionMaskMovement" width="auto" :close-on-back="false">-->
    <!--      {{detectionMaskMovement?.label}}-->
<!--      <bg-detection-mask-for-movement-->
<!--          :movement="detectionMaskMovement"-->
<!--          @close="detectionMaskMovement=null"-->
<!--      ></bg-detection-mask-for-movement>-->
    <!--    </v-dialog>-->
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

    <bg-movement-filters :entity-manager="entityManager" :filters="listFilters"
                         @update:filters="updateMovements"></bg-movement-filters>
    <v-data-table
        density="compact"
        :headers="headers"
        :items="movements"
        :items-per-page="25"
        class="elevation-1"
    >
      <template v-slot:item.actions="{ item }">
        <v-icon @click="openDetectionMask(item)" :icon="item.detectionMask?'mdi-star':'mdi-star-outline'"
                :color="getProgressColor(item.detectionMask?.score??-1)"/>
        <v-icon @click="console.log(item);movementModal=item.clone()">mdi-pencil</v-icon>
        <v-icon @click="deleteMovement(item)">mdi-delete</v-icon>
      </template>

      <template v-slot:item.date="{ item, value }">
        {{ formatDate(value) }}
      </template>
      <template v-slot:item.category.envelope.name="{ item, value }">
        {{item.category?.envelope?.color}} =>
        {{colorMapping[item.category?.envelope?.color]}}
        <v-chip :color="colorMapping[item.category?.envelope?.color]??null">{{ value }}</v-chip>
       -- {{value}}
      </template>
      <template v-slot:item.amount="{ item }">
        <v-icon v-if="item.amount>0" color="green">mdi-plus</v-icon>
        <v-icon v-else color="red">mdi-minus</v-icon>
        {{ item.amount }}
      </template>

      <template v-slot:item.category="{ item }">
        <v-autocomplete
            density="compact"
            :items="categories"
            item-title="name"
            item-value="id"
            v-model="item.categoryId"
            label="Catégorie"
            required
            hide-details
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
import BgDetectionMaskList from "../../component/detection-mask-list.vue";
import DetectionMask from "../../Data/Entity/DetectionMask";
import BgDetectionMaskEditForm from "../../Data/Form/BgDetectionMaskEditForm.vue";
import BgMovementFilters, {ListFilters} from "./component/MovementFilters.vue";
import EqualsAnyFilter from "../../../custom/npm-src/SynergyTS-npm/Data/Criteria/Filter/EqualsAnyFilter";
import ContainsFilter from "../../../custom/npm-src/SynergyTS-npm/Data/Criteria/Filter/ContainsFilter";

const entityManager: EntityManager = store.entityManager;
const movementRepository = entityManager.getRepository(Movement);
const budgetRepository = entityManager.getRepository(Budget);

type DataTableHeader = { title: string, value: string, sortable?: boolean, width?: string | number };
const MovementList = defineComponent({
  components: {
    BgMovementFilters,
    BgDetectionMaskEditForm, BgDetectionMaskList, ImportModal, BgCategoryEditForm, BgMovementEditForm
  },
  data(): {
    currentCategorySearch: string,
    currentMovement: Movement|null,
    detectionMaskMovement: Movement|null,

    movements: Movement[],
    movementModal: null | Movement,
    categoryModal: null | Category
    detectionMaskModal: null | DetectionMask
    entityManager: EntityManager,
    budgetId: null | number,
    budget: null | Budget,
    headers: DataTableHeader[],
    showImportModal: boolean,
    listFilters: ListFilters
    updateTimeout?:number,
    colorMapping: Record<string, string>
  } {
    let entityManager: EntityManager = store.entityManager;
    return {
      currentCategorySearch: '',
      detectionMaskMovement: null,
      detectionMaskModal: null,
      currentMovement: null,
      movements: [],
      movementModal: null,
      categoryModal: null,
      entityManager: entityManager,
      budgetId: null,
      budget: null,
      colorMapping : {
        'success': 'green-darken-2',
        'warning': 'orange-darken-2',
        'danger': 'deep-orange-darken-2'
      },
      headers: [
        {title: 'Amount', value: 'amount', width: "1"},
        {title: 'Date', value: 'date', width: "1"},
        {title: 'Libellé', value: 'label', width: "3"},
        {title: 'Catégorie', value: 'category', sortable: false, width: "1"},
        {title: 'Enveloppe', value: 'category.envelope.name', sortable: false, width: "1"},
        {title: 'Actions', value: 'actions', sortable: false, width: "1"},
      ],
      showImportModal: false,
      listFilters: {
        categoryId: [],
        envelopeId: [],
        label: ''
      }
    }
  }, computed: {
    categories() {
      return store.entityManager.getRepository(Category).getItems();
    }
  },
  watch: {},
  methods: {
    getProgressColor(score: number) {
      if (score > 80) {
        return 'success';
      } else if (score > 50) {
        return 'warning';
      } else if (score > 0) {
        return 'error';
      } else {
        return '';
      }
    },
    updateMovements() {
      clearTimeout(this.updateTimeout);
      this.updateTimeout = setTimeout(() => {
        this.initMovements();
      }, 300);
    },
    formatDate(date: string) {
      return new Date(date).toLocaleDateString();
    },
    playDetection(detectionMask: DetectionMask) {
      console.log('play detection', detectionMask);
    },
    openDetectionMask(movement: Movement) {
      if (movement.detectionMask) {
        this.detectionMaskModal = movement.detectionMask.clone();
      } else {
        let detectionMask = new DetectionMask();
        detectionMask.score = 100;
        detectionMask.mask = movement.label;
        detectionMask.categoryId = movement.categoryId;
        this.detectionMaskModal = detectionMask;
      }
    },
    initMovements() {
      if (this.budgetId) {
        let criteria = new Criteria();
        criteria
            .addFilter(new EqualsFilter('budget.id', this.budgetId))
            .addSort(new FieldSort('name', 'asc'))
        // .addSort(new CustomSort((a: Movement, b:Movement) => {
        //   return a.name.localeCompare(b.name)
        // }))
        ;
        let categoryIds = this.listFilters.categoryId;
        if (categoryIds && categoryIds.length > 0) {
          criteria.addFilter(new EqualsAnyFilter('categoryId', categoryIds));
        }
        let envelopeIds = this.listFilters.envelopeId;
        if (envelopeIds && envelopeIds.length > 0) {
          criteria.addFilter(new EqualsAnyFilter('category.envelopeId', envelopeIds));
        }
        if (this.listFilters.label) {
          criteria.addFilter(new ContainsFilter('label', this.listFilters.label));
        }

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
    this.initBudget();
    this.initMovements();
  },
});
export default MovementList;
</script>

<style scoped>

</style>
