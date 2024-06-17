<template>

    <v-card v-if="user" :title="modalTitle(user)">
      <v-card-text>

        <!--email-->
        <v-text-field v-model.number="user.email"
                      :label="t('budget.entities.User.fields.email')" required>
        </v-text-field>

        <!--_entityName-->
        <v-text-field v-model.number="user._entityName"
                      :label="t('budget.entities.User.fields._entityName')" required>
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
import User from "../Entity/User";
import {useLocale} from "vuetify";
import Entity from "@efrogg/synergy/Data/Entity";
// import SpDateField from "../Form/SpDateField.vue";
import {Ref, ref, onMounted} from "vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";


const props = defineProps({
  entityManager: {
    type: EntityManager,
    required:true
  },
  user: {
    type: User,
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
      .save(props.user)
      .then(() => {
        emit('save', props.user);
      })
      .catch((e: any) => {
        console.error(e);
      });
}

</script>
