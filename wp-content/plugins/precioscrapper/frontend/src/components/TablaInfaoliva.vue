<template>
  <div>
    <table class="cs-tabla-precio" v-if="tablaDatos.precios.length > 0">
      <thead>
      <tr>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <th colspan="2">Precio del aceite: <span v-text="tablaDatos.origen"></span></th>
      </tr>
      <tr>
        <th>
          <span v-text="tablaDatos.fecha_hasta"></span>
        </th>
        <th>Precios €/Kg</th>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td>Aceite de Oliva Virgen Extra</td>
        <td>
          <span
              v-text="(tablaDatos.precios[0].precio_historico != '0.000' ? tablaDatos.precios[0].precio_historico + '€' : 'S/C')"></span>
          <span v-if="tablaDatos.precios[0].precio_historico != '0.000'" class="precio-metadata">
            <span v-if="tablaDatos.precios[0].diferencial == '0.000'" v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-equal price-equal"></i>
            </span>
            <span v-else-if="(tablaDatos.precios[0].diferencial).indexOf('-') != -1 " v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-down price-down"></i>
              <span v-text="tablaDatos.precios[0].diferencial.substring(1)" class="price-down"></span>
            </span>
            <span v-else v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-up price-up"></i>
              <span v-text="tablaDatos.precios[0].diferencial" class="price-up"></span>
            </span>
          </span>
        </td>
      </tr>
      <tr>
        <td>Aceite de Oliva Virgen</td>
        <td>
          <span
              v-text="(tablaDatos.precios[1].precio_historico != '0.000' ? tablaDatos.precios[1].precio_historico + '€': 'S/C')"></span>
          <span v-if="tablaDatos.precios[1].precio_historico != '0.000'" class="precio-metadata">
            <span v-if="tablaDatos.precios[1].diferencial == '0.000'" v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-equal price-equal"></i>
            </span>
            <span v-else-if="(tablaDatos.precios[1].diferencial).indexOf('-') != -1 " v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-down price-down"></i>
              <span v-text="tablaDatos.precios[1].diferencial.substring(1)" class="price-down"></span>
            </span>
            <span v-else v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-up price-up"></i>
              <span v-text="tablaDatos.precios[1].diferencial" class="price-up"></span>
            </span>
          </span>
        </td>
      </tr>
      <tr>
        <td>Aceite de Oliva Lampante</td>
        <td>
          <span
              v-text="(tablaDatos.precios[2].precio_historico != '0.000' ? tablaDatos.precios[2].precio_historico + '€' : 'S/C')"></span>

          <span v-if="tablaDatos.precios[2].precio_historico != '0.000'" class="precio-metadata">
            <span v-if="tablaDatos.precios[2].diferencial == '0.000'" v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-equal price-equal"></i>
            </span>
            <span v-else-if="(tablaDatos.precios[2].diferencial).indexOf('-') != -1 " v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-down price-down"></i>
              <span v-text="tablaDatos.precios[2].diferencial.substring(1)" class="price-down"></span>
            </span>
            <span v-else v-text="">
              <span class="price-spacer"></span>
              <i class="mdi mdi-arrow-up price-up"></i>
              <span v-text="tablaDatos.precios[2].diferencial" class="price-up"></span>
            </span>
          </span>
        </td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td>
          <i class="mdi mdi-information"></i>
          Precio del aceite en Jaén | Infaoliva.com

        </td>
        <td>
          S/C: Sin cotización
        </td>
      </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
export default {
  name: "TablaInfaoliva",
  data() {
    return {
      tablaDatos: {
        precios: []
      },
    }
  },
  mounted() {
    axiosGetApi('/tabla/infaoliva', (res) => {
      this.tablaDatos = res.data;
    });
  }
}
</script>
<style scoped lang="scss">

.cs-tabla-precio {
  table-layout: fixed;
  // only the first set applies
  thead {
    tr:first-child {
      td:nth-child(2) {
        width: 35%;
      }
    }
  }

  font-family: "Cabin", sans-serif !important;

  tr {
    th, td {
      padding: 0 5px !important;
      border: 1px solid #E6E6E6 !important;
      //font-weight: bold !important;
    }

    th:hover, td:hover, &:hover {
      background: inherit !important;
      //background: none;
    }
  }

  thead {
    tr:nth-child(2) {
      &:hover {
        background: #333 !important;
      }

      th {
        font-size: 1.5rem !important;
        background: #333;
        color: #fff;
      }
    }

    tr:nth-child(3) {
      &:hover {
        background: #CEAC02 !important;
      }

      th {
        font-size: 1.5rem !important;
        color: #fff;
        background: #CEAC02;
        min-width: 110px;
      }
    }

    tr th {
      line-height: 3rem !important;
      font-weight: bold !important;
    }
  }

  tbody {
    td {
      color: #454442;
      line-height: 2 !important;
    }
  }

  tfoot {
    td {
      font-style: italic !important;
      font-size: .85rem !important;
      color: #A1A1A1;
      line-height: 1.2rem !important
    }
  }

  .price-up {
    color: #2e7d32 !important;
  }

  .price-down {
    color: #bf360c !important;
  }

  .price-equal {
    color: #b0bec5 !important;
  }

  .price-spacer {
    width: 15px;
    display: inline-block;
  }
}

@media only screen and (max-width: 620px) {
  .cs-tabla-precio {
    thead{
      tr:first-child{
        td:nth-child(2){
          width: 115px;
        }
      }
    }
    thead {
      tr:nth-child(2) {
        th {
          font-size: 1.25rem !important;
        }
      }

      tr:nth-child(3) {
        th {
          font-size: 1.25rem !important;
        }
      }

      tr th {
        line-height: 3rem !important;
      }
    }

    tbody {
      td {
        font-size: 1.1rem !important;
      }
    }

    tfoot {
      td {
        font-size: .8rem !important;
      }
    }

    .precio-metadata {
      .mdi {
        display: none;
      }

      .price-spacer {
        width: 5px;
      }
    }
  }
}
</style>
