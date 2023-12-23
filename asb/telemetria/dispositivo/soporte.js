const { checkInternetConnection, caracteristicasD } = require("./network");
const modbus = require("jsmodbus");
const moment = require("moment");
const { sql } = require("./db");
const mqtt = require("mqtt");
const cron = require("cron");
const net = require("net");

let date = new Date();
var topicBase, mqttUrl, clientMQTT, count;
const clientId = `equipoAsb:${Math.floor(Math.random() * (10000 - 1 + 1) + 1)}`;
var sqlP = process.env.queryParametros;
const opcionesMqtt = {
  clientId: clientId,
  clean: true,
  keepalive: 5000,
  protocolVersion: 5,
  connectTimeout: 1000,
  reconnectPeriod: 5000,
};
const updateDate = new cron.CronJob("0 */5 * * *", function () {
  date = new Date();
});
updateDate.start();

let consultaPlc = (data, clientPLC) => {
  topicBase = `asb/proyecto${data.folio}/${data.clave}`;
  mqttUrl = `ws://${data.mqttHost}:${data.mqttPort}/mqtt`;
  clientMQTT = mqtt.connect(mqttUrl, opcionesMqtt);

  clientMQTT.on("connect", () => {
    suscribirseATopico(clientMQTT, `${topicBase}/#`, async () => {
      setInterval(async () => {
        try {
          const resultP = await plc(sqlP, clientPLC, "N");
          //console.log("Datos a enviar", resultP);
          //console.log(data);

          publicarMensaje(
            clientMQTT,
            `${topicBase}/data`,
            JSON.stringify(resultP),
            () => {
              console.log("datos enviados");
            }
          );
        } catch (err) {
          console.error("Error al consultar los parámetros:", err);
          process.exit(0);
        }
      }, 10000);
    });
  });

  clientMQTT.on("message", async (topic, message) => {
    message = JSON.parse(message);
    console.log(topic);
    if (topic.includes(`${topicBase}/sql/dispositivos/update`)) {
      try {
        const params = [
          message.clave,
          message.mqttTime,
          message.mqttHost,
          message.mqttPort,
          message.plcHost,
          message.plcPort,
          message.mysqlTime,
          message.longitud,
          message.latitud,
          moment(message.licencia).format("YYYY-MM-DD HH:mm:ss"),
          message.id,
        ];
        const sqlP = process.env.queryUpdateD;
        const result = await sql(sqlP, params);
        console.log(result);
        process.exit(0);
      } catch (error) {
        console.log("Error al consultar", error);
        process.exit(0);
      }
    } else if (topic.includes(`${topicBase}/sql/parametros/update`)) {
      try {
        const params = [
          message.tipo,
          message.addr,
          message.nombre,
          message.descripcion,
          message.permiso,
          message.um,
          message.id,
        ];
        const sqlP = process.env.queryParametrosU;
        const result = await sql(sqlP, params);
        console.log(result);
        process.exit(0);
      } catch (error) {
        console.log("Error al consultar", error);
        process.exit(0);
      }
    } else if (topic.includes(`${topicBase}/sql/query/data`)) {
      try {
        console.log(message);
        const result = await sql(message, []);
        console.log(result);
        publicarMensaje(
          clientMQTT,
          `${topicBase}/sql/query/result`,
          JSON.stringify(result),
          () => {
            console.log("datos enviados");
          }
        );
      } catch (error) {
        console.log("Error al consultar", error);
        process.exit(0);
      }
    } else if (topic.includes(`${topicBase}/sql/query/on_off`)) {
      accion = message.accion == "on" ? true : false;

      clientPLC
        .writeSingleCoil(message.Addr, accion)
        .then((result) => {
          const params = {
            message: "success",
            addr: message.Addr,
            status: accion,
          };
          publicarMensaje(
            clientMQTT,
            `${topicBase}/sql/query/on_off/result`,
            JSON.stringify(params),
            () => {
              console.log("datos enviados");
            }
          );
        })
        .catch(() => {
          const params = {
            message: "error",
            addr: message.Addr,
            status: accion,
          };
          publicarMensaje(
            clientMQTT,
            `${topicBase}/sql/query/on_off/result`,
            JSON.stringify(params),
            () => {
              console.log("datos enviados");
            }
          );
          console.error(arguments);
          process.exit(0);
        });
    } else if (topic.includes(`${topicBase}/detalles/dispositivo`)) {
      publicarMensaje(
        clientMQTT,
        `${topicBase}/detalles/dispositivo/result`,
        JSON.stringify(caracteristicasD()),
        () => {
          console.log("datos enviados");
        }
      );
    }
  });

  clientMQTT.on("disconnect", () => {
    console.log("Desconectado");
    process.exit(0);
  });
  clientMQTT.on("reconnect", () => {
    console.log("Reconectando");
    process.exit(0);
  });
  clientMQTT.on("error", () => {
    console.log("Error al conectar");
    process.exit(0);
  });
  clientMQTT.on("offline", () => {
    console.log("Revisa tu conexión a internet");
    process.exit(0);
  });
};
let plc = async (sqlP, clientPLC, save) => {
  try {
    const resultP = await sql(sqlP, []);
    const listaParametros = resultP.data;
    const datosObtenidos = await obtenerDatos(clientPLC, listaParametros, save);
    return datosObtenidos;
  } catch (err) {
    console.error("Error al consultar los parámetros:", err);
    process.exit(0);
  }
};
let suscribirseATopico = (clientMQTT, topic, callback) => {
  clientMQTT.subscribe(topic, (error) => {
    if (!error) {
      if (typeof callback === "function") {
        callback();
      }
    } else {
      console.log(`Error al suscribirse a ${topic}`, error);
      process.exit(0);
    }
  });
};

let publicarMensaje = (clientMQTT, topic, data, callback) => {
  clientMQTT.publish(topic, data, (error) => {
    if (!error) {
      if (typeof callback === "function") {
        callback();
      }
    } else {
      console.log(`Error al publicar mensaje en ${topic}`, error);
      process.exit(0);
    }
  });
};

let obtenerDatos = async (clientPLC, listaParametros, save) => {
  const fecha = `${date.getFullYear()}/${
    date.getMonth() + 1
  }/${date.getDate()}`;
  const hora = `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`;
  const sendArray = [];

  try {
    if (listaParametros.length > 0) {
      for (const paramet of listaParametros) {
        let valor = numberRandom();

        const sendData = {
          idparametro: paramet.id,
          fecha: fecha,
          hora: hora,
          addr: paramet.addr,
          tipo: paramet.tipo,
          clasificacion: paramet.clasificacion,
          descripcion: paramet.descripcion,
          permiso: paramet.permiso,
          um: paramet.um,
          valor: valor,
          guardar: save,
        };

        sendArray.push(sendData);
      }
    } else {
      console.log("No se encontraron parámetros");
      process.exit(0);
    }

    return sendArray;
  } catch (err) {
    console.error("Error al obtener datos:", err);
    return sendArray;
    process.exit(0);
  }
};

let leerRegistroBit = (clientPLC, addr, cantidad) => {
  return new Promise((resolve, reject) => {
    clientPLC
      .readCoils(addr, cantidad)
      .then((resultPLC) => {
        let valorBit = resultPLC.response._body._valuesAsArray[0];
        resolve(valorBit);
      })
      .catch((error) => {
        reject(error);
      });
  });
};

let leerRegistroFloat = (clientPLC, addr, cantidad) => {
  return new Promise((resolve, reject) => {
    clientPLC
      .readHoldingRegisters(addr, cantidad)
      .then((resultPLC) => {
        let floatArray = resultPLC.response._body._valuesAsArray;

        let buffer = Buffer.allocUnsafe(4);
        buffer.writeUInt16BE(floatArray[0], 2);
        buffer.writeUInt16BE(floatArray[1], 0);
        let valorFloat = buffer.readFloatBE(0).toFixed(2);

        resolve(valorFloat);
      })
      .catch((error) => {
        reject(error);
      });
  });
};

let leerRegistroInt = (clientPLC, addr, cantidad) => {
  return new Promise((resolve, reject) => {
    clientPLC
      .readHoldingRegisters(addr, cantidad)
      .then((resultPLC) => {
        let valorInt = resultPLC.response._body._valuesAsArray[0];
        resolve(valorInt);
      })
      .catch((error) => {
        reject(error);
      });
  });
};

let numberRandom = () => {
  const numeroAleatorio = Math.random();
  const rangoMinimo = 0;
  const rangoMaximo = 100;
  const numeroEnRango =
    numeroAleatorio * (rangoMaximo - rangoMinimo) + rangoMinimo;
  const numeroConDosDecimales = parseFloat(numeroEnRango.toFixed(2));
  return numeroConDosDecimales;
};

let main = async () => {
  const query = `${process.env.dbcheckDatabaseQuery} = 'proyectoDB${process.env.numProyecto}'`;
  try {
    const result = await sql(query, []);
    if (result.data.length > 0) {
      const sqlD = `select * from dispositivos where clave='${process.env.clave}'`;
      const resultP = await sql(sqlD, []);
      var resultDatos = resultP.data[0];
      consultaPlc(resultDatos, "");
    } else {
      console.log("Base de datos no encontrada");
      process.exit(0);
    }
  } catch (error) {
    console.error(error.message);
    process.exit(0);
  }
};
main();
