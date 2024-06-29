<template>
  <v-dialog v-model="budgetModal" width="auto" :close-on-back="false">
    <bg-budget-edit-form
        :budget="budgetModal"
        :entity-manager="props.entityManager"
        @close="budgetModal=null"
        @save="onBudgetSaved"
    />
  </v-dialog>
  <v-dialog v-model="envelopeModal" width="auto" :close-on-back="false">
    <bg-envelope-edit-form
        :envelope="envelopeModal"
        :entity-manager="props.entityManager"
        @close="envelopeModal=null"
        @save="onEnvelopeSaved"
    />
  </v-dialog>

  <v-card v-if="category" :title="modalTitle(category)">
    <v-card-text>

      <!--name-->
      <v-text-field v-model.number="category.name"
                    :label="t('budget.entities.Category.fields.name')" required>
      </v-text-field>

      <!-- budget -->
      <v-row justify="end" v-if="budgetRepository">
        <v-col cols="8">
          <v-autocomplete
              :items="budgetRepository.getItems()"
              item-title="name"
              item-value="id"
              v-model="category.budgetId"
              :label="t('budget.entities.Category.fields.budget')"
              required
          ></v-autocomplete>
        </v-col>
        <v-col cols="4">
          <v-btn icon='mdi-plus-thick' @click="budgetModal=new Budget()" variant="tonal" color="primary"
                 size="small"></v-btn>
          <v-btn v-if="category.budget" icon='mdi-pencil' @click="editBudget" variant="tonal"
                 color="secondary" size="small"></v-btn>
        </v-col>
      </v-row>

      <!-- envelope -->
      <v-row justify="end" v-if="envelopeRepository">
        <v-col cols="8">
          <v-autocomplete
              :items="getEnvelopes()"
              item-title="name"
              item-value="id"
              v-model="category.envelopeId"
              :label="t('budget.entities.Category.fields.envelope')"
              required
          ></v-autocomplete>
        </v-col>
        <v-col cols="4">
          <v-btn icon='mdi-plus-thick' @click="newEnvelope" variant="tonal" color="primary"
                 size="small"></v-btn>
          <v-btn v-if="category.envelope" icon='mdi-pencil' @click="editEnvelope" variant="tonal"
                 color="secondary" size="small"></v-btn>
        </v-col>
      </v-row>
    </v-card-text>

    <v-card-actions>
      <v-container>
        <v-row justify="end">
          <v-btn prepend-icon="mdi-close" color="grey" @click="close()">Fermer</v-btn>
          <v-btn prepend-icon="mdi-check" color="primary" @click="save()">Enregistrer</v-btn>
        </v-row>
      </v-container>
    </v-card-actions>
  </v-card>
</template>
<script setup lang="ts">
import Category from "../Entity/Category";
import {useLocale} from "vuetify";
import Entity from "@efrogg/synergy/Data/Entity";
// import SpDateField from "../Form/SpDateField.vue";
import {onMounted, ref, Ref} from "vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import Repository from "@efrogg/synergy/Data/Repository";

// budget imports
import Budget from "../Entity/Budget";
import BgBudgetEditForm from "./BgBudgetEditForm.vue";
// envelope imports
import Envelope from "../Entity/Envelope";
import BgEnvelopeEditForm from "./BgEnvelopeEditForm.vue";

const budgetModal: Ref<Budget | null> = ref(null);
let budgetRepository: Repository<Budget> | null = null;
const envelopeModal: Ref<Envelope | null> = ref(null);
let envelopeRepository: Repository<Envelope> | null = null;

const props = defineProps({
  entityManager: {
    type: EntityManager,
    required: true
  },
  category: {
    type: Category,
    required: true
  }
})
const {t} = useLocale()
const emit = defineEmits(['close', 'save']);

function close() {
  emit('close')
}

onMounted(() => {
  budgetRepository = props.entityManager.getRepository(Budget)
  envelopeRepository = props.entityManager.getRepository(Envelope)
})

function modalTitle(entity: Entity): string {
  let className = entity.constructor.name;
  return entity.getId()
      ? t('budget.entities.' + className + '.listing.edit')
      : t('budget.entities.' + className + '.listing.add');
}

function newEnvelope() {
  let newEnvelope = new Envelope();
  newEnvelope.budgetId = props.category.budgetId;
  envelopeModal.value = newEnvelope;
}
function getEnvelopes() {
  let budgetId = props.category.budgetId;
  return envelopeRepository?.getItems().filter(e => e.budgetId === budgetId) ?? [];
}
function save() {
  props.entityManager
      .save(props.category)
      .then(() => {
        emit('save', props.category);
      })
      .catch((e: any) => {
        console.error(e);
      });
}

function editBudget() {
  budgetModal.value = budgetRepository?.get(props.category.budgetId)?.clone() ?? null
}

function onBudgetSaved(budget: Budget) {
  props.category.budgetId = budget.getId();
  budgetModal.value = null;
}
function editEnvelope() {
  envelopeModal.value = envelopeRepository?.get(props.category.envelopeId)?.clone() ?? null
}

function onEnvelopeSaved(envelope: Envelope) {
  props.category.envelopeId = envelope.getId();
  envelopeModal.value = null;
}

</script>
