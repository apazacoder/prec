<template>
  <div>
    <div class="cs-tabla-wrapper">
      <table class="cs-tabla-precio">
        <thead>
        <tr>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <th colspan="4">Histórico de precios: Infaoliva</th>
        </tr>
        <tr>
          <th>Fecha</th>
          <th>Virgen extra</th>
          <th>Virgen</th>
          <th>Lampante</th>
        </tr>
        </thead>
        <tbody>

        <tr v-if="tablaDatos.extra_virgen.length == 0">
          <td colspan="4">
            Sin datos
          </td>
        </tr>

        <tr v-for="(precio, index) in tablaDatos.extra_virgen" :key="'hpa'+index">
          <td>
            <span
                v-text="(tablaDatos.extra_virgen[index].fecha_precio_rango != '0.000') ? tablaDatos.extra_virgen[index].fecha_precio_rango : 'S/C'"></span>
          </td>
          <td>
            <span
                v-text="(tablaDatos.extra_virgen[index].precio_historico != '0.000') ? tablaDatos.extra_virgen[index].precio_historico : 'S/C'"></span>
          </td>
          <td>
            <span
                v-text="(tablaDatos.virgen[index].precio_historico != '0.000') ? tablaDatos.virgen[index].precio_historico : 'S/C'"></span>
          </td>
          <td>
            <span
                v-text="(tablaDatos.lampante[index].precio_historico != '0.000') ? tablaDatos.lampante[index].precio_historico : 'S/C'"></span>
          </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
          <td colspan="4">
            <i class="mdi mdi-information"></i>
            Precios del aceite en Jaén | Infaoliva.com - S/C: Sin cotización
          </td>
        </tr>
        </tfoot>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: "TablaHistoricoPreciosInfaoliva",
  data() {
    return {
      tablaDatos: {
        extra_virgen: [],
        virgen: [],
        lampante: []
      },
    }
  },
  mounted() {
    axiosGetApi('/tabla/historico/precios/infaoliva', (res) => {
      this.tablaDatos = res.data;
    });
  }
}
</script>
<style scoped lang="scss">
.cs-tabla-wrapper{
  overflow-x: auto;
}
.cs-tabla-precio{
  table-layout: fixed;
  min-width: 400px;
  thead{
    tr:first-child{
      td:nth-child(1){
        width: 190px;
      }
      td:nth-child(n+2){
        min-width: 100px;
      }
    }
  }
  thead tr:nth-child(3) th:first-child, tbody td:first-child {
    position: sticky;
    left: 0;
  }
  tbody td:first-child{
    background: #fff;
    outline: 1px solid #eee;
  }
}


.cs-tabla-precio {
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
      //&:nth-child(2){
      //  text-align: center;
      //}
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
    thead {
      tr:first-child {
        td:nth-child(1) {
          width: 80px;
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
