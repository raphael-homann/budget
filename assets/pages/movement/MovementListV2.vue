<template>
  <h1>edit movements (budget : {{ budget?.name }} {{ budget?.id }})</h1>
  <v-container>
    <ul>
      <li v-for="movement in movements">
        {{movement.date}} {{movement.category?.name}} {{movement.amount}}
      </li>
    </ul>

  </v-container>

</template>

<script lang="ts">
import {defineComponent} from "vue";
import Movement from "../../Data/Entity/Movement";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import Budget from "../../Data/Entity/Budget";
import routeParamsMixin from "../../mixin/routeParamsMixin";
import RepositoryManager from "@efrogg/synergy/Data/RepositoryManager";
import Category from "../../Data/Entity/Category";
import Envelope from "../../Data/Entity/Envelope";
import Criteria from "../../../custom/npm-src/SynergyTS-npm/Data/Criteria/Criteria";
import EqualsFilter from "../../../custom/npm-src/SynergyTS-npm/Data/Criteria/Filter/EqualsFilter";
import FieldSort from "../../../custom/npm-src/SynergyTS-npm/Data/Criteria/Sort/FieldSort";

// define the entity manager
const entityManager: EntityManager = new EntityManager(
    new RepositoryManager([Budget, Movement, Category, Envelope])
);

const movementRepository = entityManager.getRepository(Movement);
const budgetRepository = entityManager.getRepository(Budget);

const MovementListV2 = defineComponent({
  mixins: [routeParamsMixin],
  data(): {
    budgetId: null | number,
    budget: null | Budget,
    movements: Array<Movement> | null
  } {
    return {
      budgetId: null,
      budget: null,
      movements: null
    }
  }, computed: {},
  methods: {
    initMovements() {
      let criteria = new Criteria();
      criteria
          .addFilter(new EqualsFilter('budget', this.budgetId))
          .addAssociation('budget')
          .addAssociation('category.envelope')
          .setLimit(10)
          // .setOffset(2)
          .addSort(new FieldSort('amount', 'desc'));
      entityManager.search(Movement, criteria).then(({result}) => {
        this.movements = result
      })
    },
    initBudget() {
      this.budgetId = this.getRouteParamInt(this.$route, 'budgetId');
      this.initMovements(); // needs budgetId
      let criteria = new Criteria();
      criteria.addFilter(new EqualsFilter('id', this.budgetId));
      entityManager.search(Budget, criteria).then(({result, fullData}) => {
        console.log('result : ', result)
        this.budget = result[0];
      });
      budgetRepository.search(criteria)

    }
  },
  unmounted() {
  },
  mounted() {
    this.initBudget();
  },
});
export default MovementListV2;
</script>

<style scoped>

</style>
