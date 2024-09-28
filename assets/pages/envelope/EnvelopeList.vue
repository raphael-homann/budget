<template>
  <h1>edit envelopes (budget : {{ budget?.name }})</h1>
  <v-container>

    <v-dialog :model-value="envelopeModal" max-width="500">
      <bg-envelope-edit-form :entity-manager="entityManager" :envelope="envelopeModal" @close="envelopeModal=null"
                             @save="envelopeModal=null"></bg-envelope-edit-form>
    </v-dialog>

    <v-btn @click="create" color="primary">Ajouter</v-btn>

    <v-data-table
        :headers="headers"
        :items="envelopes"
        :items-per-page="25"
        class="elevation-1"
    >
      <template v-slot:item.name="{ item }">
        <v-chip :color="item.finalColor">{{ item.name }}</v-chip>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon @click="envelopeModal=item.clone()">mdi-pencil</v-icon>
        <v-icon @click="deleteEnvelope(item)">mdi-delete</v-icon>
      </template>
    </v-data-table>
  </v-container>

</template>

<script lang="ts">
import {defineComponent} from "vue";
import {store} from "../../service/store";
import Envelope from "../../Data/Entity/Envelope";
import BgEnvelopeEditForm from "../../Data/Form/BgEnvelopeEditForm.vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
import Budget from "../../Data/Entity/Budget";
import Criteria from "@efrogg/synergy/Data/Criteria/Criteria";
import EqualsFilter from "@efrogg/synergy/Data/Criteria/Filter/EqualsFilter";
import FieldSort from "@efrogg/synergy/Data/Criteria/Sort/FieldSort";
import EntityChangedEvent from "@efrogg/synergy/Data/Event/EntityChangedEvent";
import ListItemChangedEvent from "@efrogg/synergy/Data/Event/ItemListChangedEvent";

const entityManager: EntityManager = store.entityManager;
const envelopeRepository = entityManager.getRepository(Envelope);
const budgetRepository = entityManager.getRepository(Budget);

const EnvelopeList = defineComponent({
  components: {BgEnvelopeEditForm},
  data(): {
    envelopes: Envelope[],
    envelopeModal: null | Envelope,
    entityManager: EntityManager,
    budgetId: null | number,
    budget: null | Budget,
    headers: { title: string, value: string, sortable?: boolean }[],
    colorMapping: { [key: string]: string }
  } {
    let entityManager: EntityManager = store.entityManager;
    return {
      envelopes: [],
      envelopeModal: null,
      entityManager: entityManager,
      budgetId: null,
      budget: null,
      headers: [
        {title: 'Name', value: 'name'},
        {title: 'Actions', value: 'actions', sortable: false}
      ],
    }
  }, computed: {},
  methods: {
    initEnvelopes() {
      console.log('initEnvelopes', this.budgetId)
      if (this.budgetId) {
        let criteria = new Criteria();
        criteria
            .addFilter(new EqualsFilter('budget.id', this.budgetId))
            .addSort(new FieldSort('name', 'asc'))
        // .addSort(new CustomSort((a: Envelope, b:Envelope) => {
        //   return a.name.localeCompare(b.name)
        // }))
        ;
        // criteria.addFilter(new CustomFilter('budget.id', (id: any) => id === this.budgetId));                          // works
        this.envelopes = envelopeRepository.search(criteria).getItems();                // works too
        // this.envelopes = envelopeRepository.findItemsBy({'budget.id': new EqualsFilter('budget.id',this.budgetId)}).getItems(); // works
        // this.envelopes = envelopeRepository.findItemsBy({'budget.id': this.budgetId}).getItems(); // works again
      }
    },
    initBudget() {
      if (this.budgetId) {
        this.budget = budgetRepository.get(this.budgetId);
        console.log('this.budget', this.budget)
      }
    },
    deleteEnvelope(envelope: Envelope) {
      this.entityManager.delete(envelope);
      // this.initEnvelopes();
    },
    edit(envelope: Envelope) {
      this.envelopeModal = envelope.clone();
      // this.$router.push({name: 'envelope-edit', params: {id: envelope.getId()}});
    },
    create() {
      let envelope = new Envelope();
      envelope.budgetId = this.budgetId;
      this.envelopeModal = envelope;
    },
    unbindRepository() {
      budgetRepository.removeEventListener(ListChangedEvent.TYPE, this.initBudget);
      envelopeRepository.removeEventListener(ListChangedEvent.TYPE, this.initEnvelopes);
      envelopeRepository.removeEventListener(ListItemChangedEvent.TYPE, this.initEnvelopes);
    },
    bindRepository() {
      budgetRepository.addEventListener(ListChangedEvent.TYPE, this.initBudget);
      envelopeRepository.addEventListener(ListChangedEvent.TYPE, this.initEnvelopes);
      envelopeRepository.addEventListener(ListItemChangedEvent.TYPE, this.initEnvelopes);   // triggers sorting and filtering
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
    this.initEnvelopes();
  },
});
export default EnvelopeList;
</script>

<style scoped>

</style>
