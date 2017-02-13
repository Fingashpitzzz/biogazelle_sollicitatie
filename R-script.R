.libPaths("C:/Users/Sander/Documents/R/win-library/2.15");
library("gdata")

# Load file manually for testing ================
# setwd("C:/wamp/www/biogazelle")
# path   <- "upload/data.csv"
# width  <- 800
# height <- 500

# Get filename from PHP =========================
args   <- commandArgs(TRUE)     
path   <- args[1]
width  <- as.numeric(args[2])
height <- as.numeric(args[3])

# Get file name =================================
file <- basename(path)
# print (file)

# Read file =====================================
if        (length(grep(pattern = "\\.csv$" , x = file) > 0)) {
  data <- read.csv(file = path)                                 # csv-file
} else if (length(grep(pattern = "\\.xls$" , x = file) > 0)) {
  data <- read.xls(xls  = path)                                 # xls-file
} else if (length(grep(pattern = "\\.xlsx$", x = file) > 0)) {
  data <- read.xls(xls  = path)                                 # xlsx-file
}

# Calculate mean for every sample ===============
process_data           <- as.data.frame(tapply(data[,2], data[,1], mean))
colnames(process_data) <- c("mean_Cq")

# Calculate global mean =========================
tot_mean               <- colMeans(process_data)

# Calculate delta-Cq ============================
process_data$delta_Cq  <- as.array(apply(process_data$mean_Cq, 1, function(x) {x - tot_mean}))

# Calculate RQ ==================================
process_data$RQ        <- as.array(apply(process_data$delta_Cq, 1, function(x) {2^x}))

# Check width and heigth ========================
if(is.na(width) == TRUE){
  width <- 500
}
if(is.na(height) == TRUE){
   height <- 500
}

# Generate graph ================================
png(filename = paste("plots/", sub("^([^.]*).*", "\\1", file), ".png", sep = ""), 
    width = width, height = height)
barplot(height = process_data$RQ, las = 1, names = rownames(process_data), cex.names = 1, 
        axis.lty = 1, ylim = c(0, max(process_data$RQ) * 1.2))
box()

mtext("RQ"           , side = 2, cex = 1.2, line = 2.5, col = "black", font = 2)
mtext("Samples"      , side = 1, cex = 1.2, line = 2.5, col = "black", font = 2)
mtext(expression(paste("C"[q],"-analysis"))
                     , side = 3, cex = 1.7, line = 1  , col = "black", font = 2)

dev.off()