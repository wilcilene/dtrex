#include <VarSpeedServo.h> // inclusão da bibliotecaa
VarSpeedServo base, waist, cotovelo, micro1, pulso, garra;// criando objeto apartir da biblioteca
#include <ArduinoJson.h>
DynamicJsonDocument doc(1024);
void setup() {
    Serial.begin(9600);
    int inicio = 90;

    garra.attach(10); // Laranja
    pulso.attach(9); //Cinza
    micro1.attach(8); //Azul
    cotovelo.attach(7); // Verde
    waist.attach(6); //Branco
    base.attach(5); //amarelo
    
    base.slowmove(inicio,50); delay (1000);  
    waist.slowmove(inicio,50); delay (1000);
    cotovelo.slowmove(inicio,50); delay (1000);
    micro1.slowmove(inicio,50); delay (1000);
    pulso.slowmove(inicio,50); delay (1000);    
    garra.slowmove(inicio,50); delay (1000);

}

void loop() {
    int angulo_base = 70; //0 - 180
    int angulo_waist = 0; //0 - 180
    int angulo_cotovelo = 100; // waist em 0 max 110
    int angulo_micro1 = 90;
    int angulo_pulso = 90;
    int angulo_garra = 14;//14 - 80

    String conteudo = "";
    char caractere = '\n';
    while (Serial.available() > 0) {
      caractere = Serial.read();
      if (caractere != '\n') {
        // Concatena valores
        conteudo.concat(caractere);
        // Aguarda buffer serial ler próximo caractere
        delay(10);
      }
    }

    if (conteudo.length() > 0) {
      Serial.println("I received: ");
      Serial.println(conteudo);
      deserializeJson(doc, conteudo);
      JsonObject obj = doc.as<JsonObject>();

      int delay_mov = 200;
//
      base.slowmove(obj[String("base")], 50); delay(delay_mov);
      waist.slowmove(obj[String("waist")],50); delay (delay_mov);
      cotovelo.slowmove(obj[String("cotovelo")],50); delay (delay_mov);
      micro1.slowmove(obj[String("micro1")],50); delay (delay_mov);
      pulso.slowmove(obj[String("pulso")],50); delay (delay_mov);
      garra.slowmove(obj[String("garra")],50); delay(delay_mov);
      
     
    }
}