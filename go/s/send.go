package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"strconv"
	"text/template"

	"github.com/streadway/amqp"
)

//Message type
type Message struct {
	BulidNo int
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Fatalf("%s: %s", msg, err)
	}
}

func send(w http.ResponseWriter, r *http.Request) {
	fmt.Println("method:", r.Method) //获取请求的方法

	r.ParseForm()

	conn, err := amqp.Dial("amqp://guest:guest@47.97.108.162:5672/")
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	q, err := ch.QueueDeclare(
		"hello", // name
		false,   // durable
		false,   // delete when unused
		false,   // exclusive
		false,   // no-wait
		nil,     // arguments
	)
	failOnError(err, "Failed to declare a queue")

	numstr := r.Form["building"][0]
	num, _ := strconv.Atoi(numstr)
	message := Message{num}

	body, err := json.Marshal(message)
	failOnError(err, "Failed to conver tJson")

	err = ch.Publish(
		"",     // exchange
		q.Name, // routing key
		false,  // mandatory
		false,  // immediate
		amqp.Publishing{
			ContentType: "text/plain",
			Body:        body,
		})
	log.Printf(" [x] Sent %s", body)

	t, _ := template.ParseFiles("goback.html")
	_ = t.Execute(w, nil)
	status, err := json.Marshal(r.Header["status"])
	w.Write(status)
	failOnError(err, "Failed to publish a message")
}

func main() {
	http.HandleFunc("/send", send)           //设置访问的路由
	err := http.ListenAndServe(":9090", nil) //设置监听的端口
	if err != nil {
		log.Fatal("ListenAndServe: ", err)
	}
}
