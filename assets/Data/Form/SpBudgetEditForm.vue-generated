<template>

    <v-card v-if="budget" :title="modalTitle(budget)">
      <v-card-text>

        <!--name-->
        <v-text-field v-model.number="budget.name"
                      :label="t('fse.entities.Budget.fields.name')" required>
        </v-text-field>
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
import Budget from "../Entity/Budget";
import {useLocale} from "vuetify";
import Entity from "Synergy/Data/Entity";
import SpDateField from "../Form/SpDateField.vue";


import entityManagerService from "Synergy/Data/EntityManager";


const props = defineProps({
  budget: {
    type: Budget,
    required: true
  }
})
const {t} = useLocale()
const emit = defineEmits(['close', 'save']);

function close() {
  emit('close')
}

function modalTitle(entity: Entity): string {
  let className = entity.constructor.name;
  return entity.getId()
      ? t('srPit.entities.' + className + '.listing.edit')
      : t('srPit.entities.' + className + '.listing.add');
}

function save() {
  entityManagerService
      .save(props.budget)
      .then(() => {
        emit('save', props.budget);
      })
      .catch((e) => {
        console.error(e);
      });
}

</script>
