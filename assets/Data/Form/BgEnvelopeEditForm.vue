<template>

  <v-card v-if="envelope" :title="modalTitle(envelope)" :color="envelope.finalColor">
    <v-card-text>

      <!--name-->
      <v-text-field :color="envelope.finalColor" v-model.number="envelope.name"
                    :label="t('budget.entities.Envelope.fields.name')" required>
      </v-text-field>


      <!--color-->
      <v-select :color="envelope.finalColor" v-model="envelope.color" :items="Envelope.colorMapping" item-title="title" item-value="id"
                 :label="t('budget.entities.Envelope.fields.color')" required>
      </v-select>

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
import Envelope from "../Entity/Envelope";
import {useLocale} from "vuetify";
import Entity from "@efrogg/synergy/Data/Entity";
// import SpDateField from "../Form/SpDateField.vue";
import {onMounted, ref, Ref} from "vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";

const props = defineProps({
  entityManager: {
    type: EntityManager,
    required: true
  },
  envelope: {
    type: Envelope,
    required: true
  }
})
const {t} = useLocale()
const emit = defineEmits(['close', 'save']);

function close() {
  emit('close')
}

onMounted(() => {
})

function modalTitle(entity: Entity): string {
  let className = entity.constructor.name;
  return entity.getId()
      ? t('budget.entities.' + className + '.listing.edit')
      : t('budget.entities.' + className + '.listing.add');
}

function save() {
  props.entityManager
      .save(props.envelope)
      .then(() => {
        emit('save', props.envelope);
      })
      .catch((e: any) => {
        console.error(e);
      });
}


</script>
