#
# This is a Shiny web application. You can run the application by clicking
# the 'Run App' button above.
#
# Find out more about building applications with Shiny here:
#
#    http://shiny.rstudio.com/
#

library(shiny)
library(shinyWidgets)
library(dplyr)
m_data <- read.csv('Mission_data.csv')
r_data <- read.csv("Relic_data.csv")

# Define UI for application that draws a histogram
ui <- fluidPage(
   
   # Application title
   titlePanel("Warframe Grind Picker"),
   
   # Sidebar with a slider input for number of bins 
   sidebarLayout(
      sidebarPanel(
         pickerInput('itemSelect',
                     'Select an Item',
                     choices = levels(r_data$Item)
         ),
         pickerInput('missionTypeSelect',
                     'Select a mission Type',
                     choices = levels(m_data$Mission.Type),
                     multiple = TRUE,
                     options = list(
                       `actions-box` = TRUE,
                       `deselect-all-text` = "None",
                       `select-all-text` = "All",
                       `none-selected-text` = "None"
                      )
         ),
         pickerInput(
           'sortSelect',
           'Sort by',
           choices = colnames(m_data),
           selected = "Planet"
         ),
         pickerInput(
           'secondSortSelect',
           'Sort by',
           choices = colnames(m_data),
           selected = "Planet"
         ),
         switchInput(inputId = "id", value = TRUE),
         switchInput(inputId = "id", value = TRUE)
         
      ),
      
      # Show a plot of the generated distribution
      mainPanel(
         tableOutput("tab")
      )
   )
)

# Define server logic required to draw a histogram
server <- function(input, output) {
  output$tab <- renderTable({
    m_data[(m_data$Item %in% r_data[r_data$Item == input$itemSelect,]$LongName & m_data$Mission.Type %in% input$missionTypeSelect),] %>% 
      arrange_at(input$secondSortSelect) %>%
      arrange_at(input$sortSelect)
  })
}

# Run the application 
options(shiny.port = 20000)
options(shiny.host = "192.168.72.112")
shinyApp(ui = ui, server = server)

